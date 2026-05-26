<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PushSubscriptionController extends Controller
{
    public function config(): JsonResponse
    {
        $subscription = request()->user()
            ? request()->user()->pushSubscriptions()->where('is_enabled', true)->latest()->first()
            : null;

        $publicKey = config('services.webpush.vapid.public_key');

        return response()->json([
            'enabled' => (bool) config('services.webpush.enabled'),
            'send_enabled' => (bool) config('services.webpush.enabled'),
            'can_subscribe' => filled($publicKey),
            'public_key' => $publicKey,
            'vapidPublicKey' => $publicKey,
            'types' => Notification::TYPE_LABELS,
            'subscription_count' => request()->user()
                ? request()->user()->pushSubscriptions()->where('is_enabled', true)->count()
                : 0,
            'preferences' => $subscription?->preferences ?? [
                'enabled' => true,
                'types' => [],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
            'keys' => ['required', 'array'],
            'keys.p256dh' => ['required', 'string', 'max:500'],
            'keys.auth' => ['required', 'string', 'max:255'],
            'content_encoding' => ['nullable', 'string', Rule::in(['aes128gcm', 'aesgcm'])],
            'preferences' => ['nullable', 'array'],
            'preferences.enabled' => ['nullable', 'boolean'],
            'preferences.types' => ['nullable', 'array'],
            'preferences.types.*' => ['boolean'],
        ]);

        $subscription = PushSubscription::withTrashed()->updateOrCreate(
            ['endpoint_hash' => PushSubscription::hashEndpoint($data['endpoint'])],
            [
                'user_id' => $request->user()->id,
                'endpoint' => $data['endpoint'],
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
                'content_encoding' => $data['content_encoding'] ?? 'aes128gcm',
                'user_agent' => (string) $request->userAgent(),
                'is_enabled' => (bool) data_get($data, 'preferences.enabled', true),
                'preferences' => $this->cleanPreferences($data['preferences'] ?? []),
                'deleted_at' => null,
            ]
        );

        if ($subscription->trashed()) {
            $subscription->restore();
        }

        return response()->json([
            'ok' => true,
            'subscription_id' => $subscription->id,
        ]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
            'preferences' => ['required', 'array'],
            'preferences.enabled' => ['nullable', 'boolean'],
            'preferences.types' => ['nullable', 'array'],
            'preferences.types.*' => ['boolean'],
        ]);

        $subscription = PushSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']))
            ->firstOrFail();

        $subscription->update([
            'is_enabled' => (bool) data_get($data, 'preferences.enabled', true),
            'preferences' => $this->cleanPreferences($data['preferences']),
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:2000'],
        ]);

        PushSubscription::query()
            ->where('user_id', $request->user()->id)
            ->where('endpoint_hash', PushSubscription::hashEndpoint($data['endpoint']))
            ->delete();

        return response()->json(['ok' => true]);
    }

    private function cleanPreferences(array $preferences): array
    {
        $allowedTypes = array_keys(Notification::TYPE_LABELS);
        $types = collect($preferences['types'] ?? [])
            ->only($allowedTypes)
            ->map(fn ($enabled) => (bool) $enabled)
            ->all();

        return [
            'enabled' => (bool) ($preferences['enabled'] ?? true),
            'types' => $types,
        ];
    }
}
