<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\CommunityReport;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\LiveChatMessage;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommunityReportController extends Controller
{
    public function reportTopic(Request $request, ForumTopic $topic): RedirectResponse
    {
        abort_unless($topic->status === 'published', 404);

        return $this->store($request, $topic);
    }

    public function reportPost(Request $request, ForumPost $post): RedirectResponse
    {
        abort_unless($post->status === 'approved' && $post->topic?->status === 'published', 404);

        return $this->store($request, $post);
    }

    public function reportLiveChatMessage(Request $request, LiveChatMessage $message): RedirectResponse
    {
        abort_unless($message->status === 'approved', 404);

        return $this->store($request, $message);
    }

    private function store(Request $request, ForumTopic|ForumPost|LiveChatMessage $subject): RedirectResponse
    {
        $data = Validator::make($request->all(), [
            'reason' => ['required', Rule::in(array_keys(CommunityReport::REASONS))],
            'details' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        if ((int) $subject->user_id === (int) $request->user()->id) {
            return back()->with('error', 'Kendi iceriginizi raporlayamazsiniz.');
        }

        try {
            $report = CommunityReport::create([
                'user_id' => $request->user()->id,
                'reportable_type' => $subject::class,
                'reportable_id' => $subject->getKey(),
                'reason' => $data['reason'],
                'details' => $data['details'] ?? null,
                'status' => 'pending',
                'subject_ai_risk_score' => (int) ($subject->ai_risk_score ?? 0),
                'subject_ai_risk_label' => (string) ($subject->ai_risk_label ?? 'low'),
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->with('error', 'Bu icerigi daha once raporladiniz.');
        }

        NotificationHelper::sendToModerators(
            type: 'community_report_created',
            title: 'Yeni topluluk raporu',
            message: $request->user()->name . ' bir ' . $report->reportableLabel() . ' raporladi.',
            url: '/admin/community-reports',
            data: [
                'report_id' => $report->id,
                'reason' => $report->reason,
                'ai_risk_score' => $report->subject_ai_risk_score,
                'ai_risk_label' => $report->subject_ai_risk_label,
            ]
        );

        return back()->with('success', 'Raporunuz moderasyon ekibine iletildi.');
    }
}
