<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PollController extends Controller
{
    public function index(): View
    {
        $polls = Poll::active()
            ->with(['activeOptions', 'options'])
            ->latest()
            ->paginate(12);

        return view('frontend.polls', compact('polls'));
    }

    public function show(Request $request, string $slug): View
    {
        $poll = Poll::active()
            ->with(['activeOptions', 'options'])
            ->where('slug', $slug)
            ->firstOrFail();

        $poll->increment('views');

        return view('frontend.poll-detail', [
            'poll' => $poll,
            'hasVoted' => $poll->hasVoteFrom($request),
            'totalVotes' => $poll->totalVotes(),
        ]);
    }

    public function vote(Request $request, Poll $poll): RedirectResponse
    {
        abort_unless($poll->is_active, 404);
        abort_if($poll->starts_at && $poll->starts_at->isFuture(), 404);
        abort_if($poll->ends_at && $poll->ends_at->isPast(), 404);

        if ($poll->require_login || (! $poll->allow_guests && ! $request->user())) {
            return back()->with('poll_error', 'Bu ankete oy vermek için giriş yapmanız gerekiyor.');
        }

        $optionExistsRule = Rule::exists('poll_options', 'id')
            ->where('poll_id', $poll->id);

        $rules = $poll->allow_multiple
            ? [
                'option_ids' => ['required', 'array', 'min:1'],
                'option_ids.*' => ['integer', $optionExistsRule],
            ]
            : [
                'option_id' => ['required', 'integer', $optionExistsRule],
            ];

        $data = $request->validate($rules);

        $optionIds = $poll->allow_multiple
            ? collect($data['option_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values()
            : collect([(int) $data['option_id']]);

        $validOptionIds = PollOption::query()
            ->where('poll_id', $poll->id)
            ->where('is_active', true)
            ->whereIn('id', $optionIds)
            ->pluck('id');

        if ($validOptionIds->count() !== $optionIds->count()) {
            return back()->with('poll_error', 'Geçersiz anket seçeneği.');
        }

        $voterKey = $poll->voterKey($request);

        if ($poll->votes()->where('voter_key', $voterKey)->exists()) {
            return back()->with('poll_error', 'Bu ankete daha önce oy verdiniz.');
        }

        DB::transaction(function () use ($poll, $request, $voterKey, $validOptionIds) {
            foreach ($validOptionIds as $optionId) {
                $poll->votes()->create([
                    'poll_option_id' => $optionId,
                    'user_id' => $request->user()?->id,
                    'session_id' => $request->session()->getId(),
                    'ip_hash' => hash('sha256', (string) $request->ip()),
                    'voter_key' => $voterKey,
                    'user_agent' => str((string) $request->userAgent())->limit(255)->toString(),
                ]);

                PollOption::whereKey($optionId)->increment('votes_count');
            }
        });

        return back()->with('poll_success', 'Oyunuz kaydedildi. Teşekkürler.');
    }
}
