<?php

namespace App\Http\Controllers;
use App\Models\Video;
use App\Models\Gallery;
use App\Helpers\ActivityLogger;
use App\Helpers\NotificationHelper;
use App\Models\Announcement;
use App\Models\Comment;
use App\Models\News;
use App\Models\SiteSetting;
use App\Models\UserPunishment;
use App\Support\CommunitySafety;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function storeNews(Request $request, News $news): RedirectResponse
    {
        return $this->storeComment($request, $news);
    }

    public function storeAnnouncement(Request $request, Announcement $announcement): RedirectResponse
    {
        return $this->storeComment($request, $announcement);
    }
public function storeVideo(Request $request, Video $video): RedirectResponse
{
    return $this->storeComment($request, $video);
}

public function storeGallery(Request $request, Gallery $gallery): RedirectResponse
{
    return $this->storeComment($request, $gallery);
}
    private function storeComment(Request $request, $commentable): RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $content = trim($request->content);

        $activePunishment = UserPunishment::where('user_id', auth()->id())
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        if (
            $activePunishment &&
            in_array($activePunishment->type, ['mute', 'temporary_ban', 'permanent_ban'])
        ) {

            ActivityLogger::log(
                action: 'blocked_comment_attempt',
                description: auth()->user()->name . ' ceza nedeniyle yorum yapamadı.',
                properties: [
                    'user_id' => auth()->id(),
                    'punishment_type' => $activePunishment->type,
                    'content' => Str::limit($content, 200),
                ]
            );

            return back()->with(
                'error',
                'Yorum yapma yetkiniz geçici veya kalıcı olarak kısıtlanmıştır.'
            );
        }

        if ($this->hasFloodRisk()) {

            ActivityLogger::log(
                action: 'flood_comment_detected',
                description: auth()->user()->name . ' kısa sürede çok fazla yorum denedi.',
                properties: [
                    'user_id' => auth()->id(),
                    'content' => Str::limit($content, 200),
                    'ip' => $request->ip(),
                ]
            );

            return back()->with(
                'error',
                'Çok kısa sürede fazla yorum yaptınız. Lütfen biraz bekleyin.'
            );
        }

        $safety = CommunitySafety::assess($content, auth()->user(), 'comment');
        $status = $safety->shouldReject() ? 'rejected' : 'pending';
        $reason = $safety->reasons[0] ?? null;

        if (false && $this->hasBannedWords($content)) {
            $status = 'rejected';
            $reason = 'Yasaklı kelime tespit edildi.';
        }

        if (false && $this->hasLinkSpam($content)) {
            $status = 'rejected';
            $reason = 'Link spam tespit edildi.';
        }

        $comment = $commentable->comments()->create([
            'user_id' => auth()->id(),
            'content' => $content,
            'status' => $status,
            ...$safety->attributes(),
            'ip_address' => $request->ip(),
        ]);

        if ($status === 'rejected') {

            ActivityLogger::log(
                action: 'spam_comment_rejected',
                description: auth()->user()->name . ' tarafından gönderilen yorum otomatik reddedildi.',
                properties: [
                    'comment_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'reason' => $reason,
                    'ai_risk_score' => $safety->score,
                    'ai_risk_label' => $safety->label,
                    'ai_risk_reasons' => $safety->reasons,
                    'auto_punishment' => $this->shouldApplyAutoPunishment(),
                    'content' => Str::limit($content, 300),
                    'ip' => $request->ip(),
                ]
            );

            NotificationHelper::sendToModerators(
                type: 'spam_comment',
                title: 'Spam Yorum Reddedildi',
                message: auth()->user()->name . ' tarafından gönderilen yorum sistem tarafından reddedildi.',
                url: '/admin/comments',
                data: [
                    'comment_id' => $comment->id,
                    'user_id' => auth()->id(),
                    'reason' => $reason,
                    'ai_risk_score' => $safety->score,
                    'ai_risk_label' => $safety->label,
                    'reasons' => $safety->reasons,
                ]
            );

            return back()->with(
                'error',
                'Yorumunuz sistem tarafından uygunsuz içerik nedeniyle reddedildi.'
            );
        }

        if ($this->shouldApplyAutoPunishment()) {

            $rejectedCount = Comment::query()
                ->where('user_id', auth()->id())
                ->where('status', 'rejected')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            if ($rejectedCount >= 3) {

                UserPunishment::create([
                    'user_id' => auth()->id(),
                    'type' => 'mute',
                    'reason' => 'Otomatik spam/flood koruma sistemi',
                    'is_active' => true,
                    'expires_at' => now()->addHours(12),
                ]);

                ActivityLogger::log(
                    action: 'auto_punishment_applied',
                    description: auth()->user()->name . ' kullanıcısına otomatik mute uygulandı.',
                    properties: [
                        'user_id' => auth()->id(),
                        'rejected_comments' => $rejectedCount,
                        'type' => 'mute',
                    ]
                );

                NotificationHelper::sendToAdmins(
                    type: 'auto_punishment',
                    title: 'Otomatik Ceza Uygulandı',
                    message: auth()->user()->name . ' kullanıcısına otomatik mute uygulandı.',
                    url: '/admin/user-punishments',
                    data: [
                        'user_id' => auth()->id(),
                        'type' => 'mute',
                        'duration' => '12 saat',
                    ]
                );
            }
        }

        ActivityLogger::log(
            action: 'comment_submitted',
            description: auth()->user()->name . ' yeni yorum gönderdi.',
            properties: [
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'status' => $status,
                'ai_risk_score' => $safety->score,
                'ai_risk_label' => $safety->label,
                'reasons' => $safety->reasons,
                'content' => Str::limit($content, 300),
                'ip' => $request->ip(),
            ]
        );

        NotificationHelper::sendToModerators(
            type: $safety->requiresReview() ? 'community_safety_alert' : 'new_comment',
            title: $safety->requiresReview() ? 'Supheli yorum bekliyor' : 'Yeni Yorum Bekliyor',
            message: auth()->user()->name . ' yeni bir yorum gönderdi.',
            url: '/admin/comments',
            data: [
                'comment_id' => $comment->id,
                'user_id' => auth()->id(),
                'status' => $status,
                'ai_risk_score' => $safety->score,
                'ai_risk_label' => $safety->label,
                'reasons' => $safety->reasons,
            ]
        );

        return back()->with(
            'success',
            'Yorumunuz moderatör onayına gönderildi.'
        );
    }

    private function hasFloodRisk(): bool
    {
        return Comment::query()
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinute())
            ->count() >= 3;
    }

    private function hasBannedWords(string $content): bool
    {
        $bannedWords = [
            'küfür1',
            'küfür2',
            'hakaret1',
            'hakaret2',
            'spam',
            'dolandırıcılık',
        ];

        $lowerContent = Str::lower($content);

        foreach ($bannedWords as $word) {
            if (Str::contains($lowerContent, Str::lower($word))) {
                return true;
            }
        }

        return false;
    }

    private function hasLinkSpam(string $content): bool
    {
        preg_match_all('/https?:\/\/|www\.|\.com|\.net|\.org|\.xyz/i', $content, $matches);

        return count($matches[0]) >= 2;
    }

    private function shouldApplyAutoPunishment(): bool
    {
        $settings = SiteSetting::query()->first();

        return (bool) ($settings?->auto_punishment_enabled ?? false);
    }
}
