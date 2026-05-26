@php
    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->whereIn('type', [
            'new_comment',
            'spam_comment',
            'auto_punishment',
            'security',
        ])
        ->count();
@endphp

<a href="/admin"
   title="Bildirimler"
   class="relative inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">

    <span class="text-xl leading-none">🔔</span>

    @if($unreadCount > 0)
        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white shadow">
            {{ $unreadCount }}
        </span>
    @endif

</a>