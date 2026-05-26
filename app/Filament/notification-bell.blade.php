@php
    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->count();

    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->latest()
        ->limit(5)
        ->get();
@endphp

<div class="relative">
    <details class="relative">
        <summary class="list-none cursor-pointer">
            <div class="relative rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                🔔

                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
        </summary>

        <div class="absolute right-0 z-50 mt-3 w-80 rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 font-semibold dark:border-gray-700">
                Bildirimler
            </div>

            <div class="max-h-80 overflow-y-auto">
                @forelse($notifications as $notification)
                    <a href="{{ route('notifications.read', $notification) }}"
                       class="block border-b border-gray-100 px-4 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">

                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-sm font-semibold {{ ! $notification->is_read ? 'text-primary-600' : '' }}">
                                    {{ $notification->title }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($notification->message, 80) }}
                                </div>

                                <div class="mt-1 text-[11px] text-gray-400">
                                    {{ $notification->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>

                            @if(! $notification->is_read)
                                <span class="mt-1 h-2 w-2 rounded-full bg-primary-600"></span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                        Bildirim yok.
                    </div>
                @endforelse
            </div>

            <div class="border-t border-gray-200 px-4 py-3 text-center dark:border-gray-700">
                <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-primary-600">
                    Tüm bildirimleri gör
                </a>
            </div>
        </div>
    </details>
</div>