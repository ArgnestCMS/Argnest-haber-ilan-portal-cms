<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Http\Requests\StoreForumPostRequest;
use App\Http\Requests\StoreForumTopicRequest;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\ForumTopicBookmark;
use App\Models\ForumTopicLike;
use App\Models\LiveActivity;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserPunishment;
use App\Support\ForumContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function storeTopic(StoreForumTopicRequest $request): RedirectResponse
    {
        if (! $this->forumIsEnabled()) {
            return back()->with('error', 'Forum şu anda kullanıma kapalı.');
        }

        if ($this->userIsMuted()) {
            return back()->with('error', 'Forum yazma yetkiniz geçici veya kalıcı olarak kısıtlanmıştır.');
        }

        if ($this->hasTopicFloodRisk()) {
            return back()->with('error', 'Çok kısa sürede fazla konu açtınız. Lütfen biraz bekleyin.');
        }

        $content = ForumContent::sanitize((string) $request->input('content'));
        $title = trim((string) $request->string('title'));

        if (ForumContent::isEmpty($content)) {
            return back()->withInput()->withErrors(['content' => 'Konu icerigi bos olamaz.']);
        }

        if ($this->hasSpamRisk($title . ' ' . ForumContent::plainText($content))) {
            return back()->with('error', 'Konu içeriği spam filtresine takıldı.');
        }

        $category = ForumCategory::active()
            ->whereKey($request->integer('forum_category_id'))
            ->firstOrFail();

        $topic = ForumTopic::create([
            'forum_category_id' => $category->id,
            'user_id' => auth()->id(),
            'title' => $title,
            'slug' => $this->uniqueTopicSlug($title),
            'content' => $content,
            'status' => 'pending',
            'last_post_at' => now(),
            'last_post_user_id' => auth()->id(),
        ]);

        $this->notifyMentions($title . ' ' . $content, $topic);
        LiveActivity::record([
            'type' => 'forum_topic_created',
            'source' => 'forum',
            'severity' => 'info',
            'title' => 'Yeni forum konusu gönderildi',
            'message' => auth()->user()->name . ' "' . Str::limit($topic->title, 80) . '" başlıklı bir konu açtı.',
            'subject' => $topic,
            'url' => null,
            'metadata' => [
                'topic_id' => $topic->id,
                'status' => $topic->status,
            ],
        ]);

        return redirect()
            ->route('forum.index')
            ->with('success', 'Konunuz moderatör onayına gönderildi.');
    }

    public function storePost(StoreForumPostRequest $request, ForumTopic $topic): RedirectResponse
    {
        if (! $this->forumIsEnabled()) {
            return back()->with('error', 'Forum şu anda kullanıma kapalı.');
        }

        if (! $topic->category?->is_active || $topic->status !== 'published') {
            abort(404);
        }

        if ($topic->is_locked) {
            return back()->with('error', 'Bu konu cevaplara kapalı.');
        }

        if ($this->userIsMuted()) {
            return back()->with('error', 'Forum yazma yetkiniz geçici veya kalıcı olarak kısıtlanmıştır.');
        }

        if ($this->hasPostFloodRisk()) {
            return back()->with('error', 'Çok kısa sürede fazla cevap yazdınız. Lütfen biraz bekleyin.');
        }

        $content = ForumContent::sanitize((string) $request->input('content'));

        if (ForumContent::isEmpty($content)) {
            return back()->withInput()->withErrors(['content' => 'Cevap icerigi bos olamaz.']);
        }

        if ($this->hasSpamRisk(ForumContent::plainText($content))) {
            return back()->with('error', 'Cevabınız spam filtresine takıldı.');
        }

        $post = ForumPost::create([
            'forum_topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'content' => $content,
            'status' => 'pending',
            'ip_address' => request()->ip(),
        ]);

        $this->notifyTopicOwner($topic);
        $this->notifyMentions($content, $topic, $post);
        LiveActivity::record([
            'type' => 'forum_post_created',
            'source' => 'forum',
            'severity' => 'success',
            'title' => 'Yeni forum cevabı gönderildi',
            'message' => auth()->user()->name . ' "' . Str::limit($topic->title, 80) . '" konusuna cevap yazdı.',
            'subject' => $post,
            'url' => route('forum.topics.show', $topic->slug),
            'metadata' => [
                'topic_id' => $topic->id,
                'post_id' => $post->id,
                'status' => $post->status,
            ],
        ]);

        return back()->with('success', 'Cevabınız moderatör onayına gönderildi.');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        if (! $this->forumIsEnabled() || $this->userIsMuted()) {
            abort(403);
        }

        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $path = $data['image']->store('forum', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    public function toggleLike(ForumTopic $topic): RedirectResponse
    {
        abort_unless($topic->status === 'published', 404);

        if ($topic->user_id === auth()->id()) {
            return back()->with('error', 'Kendi konunuzu beğenemezsiniz.');
        }

        $like = ForumTopicLike::query()
            ->where('forum_topic_id', $topic->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete();
            $topic->user?->addForumReputation(-1);

            return back()->with('success', 'Beğeni kaldırıldı.');
        }

        ForumTopicLike::create([
            'forum_topic_id' => $topic->id,
            'user_id' => auth()->id(),
        ]);

        if ($topic->user_id && $topic->user_id !== auth()->id()) {
            $topic->user?->addForumReputation(1);

            NotificationHelper::sendToUser(
                userId: $topic->user_id,
                type: 'forum_topic_liked',
                title: 'Forum konunuz beğenildi',
                message: auth()->user()->name . ' forum konunuzu beğendi.',
                url: route('forum.topics.show', $topic->slug),
                data: ['topic_id' => $topic->id]
            );
        }

        return back()->with('success', 'Konu beğenildi.');
    }

    public function toggleBookmark(ForumTopic $topic): RedirectResponse
    {
        abort_unless($topic->status === 'published', 404);

        $bookmark = ForumTopicBookmark::query()
            ->where('forum_topic_id', $topic->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($bookmark) {
            $bookmark->delete();

            return back()->with('success', 'Favorilerden çıkarıldı.');
        }

        ForumTopicBookmark::create([
            'forum_topic_id' => $topic->id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Konu favorilere eklendi.');
    }

    private function forumIsEnabled(): bool
    {
        return (bool) (SiteSetting::query()->first()?->forum_enabled ?? false);
    }

    private function userIsMuted(): bool
    {
        return UserPunishment::query()
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereIn('type', ['mute', 'temporary_ban', 'permanent_ban'])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    private function hasTopicFloodRisk(): bool
    {
        return ForumTopic::query()
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count() >= 2;
    }

    private function hasPostFloodRisk(): bool
    {
        return ForumPost::query()
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subMinute())
            ->count() >= 3;
    }

    private function hasSpamRisk(string $content): bool
    {
        $lowerContent = Str::lower($content);
        $bannedWords = ['spam', 'dolandırıcılık', 'küfür1', 'küfür2', 'hakaret1', 'hakaret2'];

        foreach ($bannedWords as $word) {
            if (Str::contains($lowerContent, Str::lower($word))) {
                return true;
            }
        }

        preg_match_all('/https?:\/\/|www\.|\.com|\.net|\.org|\.xyz/i', $content, $matches);

        return count($matches[0]) >= 2;
    }

    private function uniqueTopicSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'forum-konusu';
        $slug = $baseSlug;
        $counter = 2;

        while (ForumTopic::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function notifyTopicOwner(ForumTopic $topic): void
    {
        if (! $topic->user_id || $topic->user_id === auth()->id()) {
            return;
        }

        NotificationHelper::sendToUser(
            userId: $topic->user_id,
            type: 'forum_topic_reply',
            title: 'Forum konunuza yeni cevap',
            message: auth()->user()->name . ' forum konunuza cevap yazdı.',
            url: route('forum.topics.show', $topic->slug),
            data: ['topic_id' => $topic->id]
        );
    }

    private function notifyMentions(string $content, ForumTopic $topic, ?ForumPost $post = null): void
    {
        preg_match_all('/@([A-Za-z0-9_.-]{3,30})/u', $content, $matches);

        $mentions = collect($matches[1] ?? [])
            ->unique()
            ->take(10);

        if ($mentions->isEmpty()) {
            return;
        }

        User::query()
            ->whereIn('name', $mentions)
            ->where('id', '!=', auth()->id())
            ->get()
            ->each(function (User $user) use ($topic, $post) {
                NotificationHelper::sendToUser(
                    userId: $user->id,
                    type: 'forum_mention',
                    title: 'Forumda sizden bahsedildi',
                    message: auth()->user()->name . ' bir forum mesajında sizden bahsetti.',
                    url: route('forum.topics.show', $topic->slug),
                    data: [
                        'topic_id' => $topic->id,
                        'post_id' => $post?->id,
                    ]
                );
            });
    }
}
