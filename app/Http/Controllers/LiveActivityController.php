<?php

namespace App\Http\Controllers;

use App\Models\LiveActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LiveActivityController extends Controller
{
    public function latest(Request $request): JsonResponse
    {
        $afterId = max(0, $request->integer('after_id'));

        $activities = LiveActivity::query()
            ->public()
            ->with('user:id,name')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->recent()
            ->take(30)
            ->get()
            ->sortByDesc('id')
            ->values()
            ->map(fn (LiveActivity $activity) => $activity->toFeedItem());

        return response()->json([
            'activities' => $activities,
            'latest_id' => $activities->max('id') ?? $afterId,
            'polled_at' => now()->format('H:i:s'),
        ]);
    }
}
