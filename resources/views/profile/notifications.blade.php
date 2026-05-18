<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    Bildirim Merkezi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Forum, topluluk ve moderasyon bildirimlerinizi buradan takip edin.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('user.notifications.readAll') }}">
                    @csrf

                    <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-800">
                        Tumunu Okundu Yap
                    </button>
                </form>

                <a href="/dashboard" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                    Panele Don
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <a href="{{ route('user.notifications') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ ! $selectedStatus && ! $selectedGroup && ! $selectedType ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Tum</div>
                    <div class="mt-2 text-2xl font-black text-slate-950">{{ $counts['all'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['status' => 'unread']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedStatus === 'unread' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Okunmamis</div>
                    <div class="mt-2 text-2xl font-black text-blue-700">{{ $counts['unread'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['status' => 'read']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedStatus === 'read' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Okundu</div>
                    <div class="mt-2 text-2xl font-black text-slate-950">{{ $counts['read'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'forum']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'forum' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Forum</div>
                    <div class="mt-2 text-2xl font-black text-red-700">{{ $counts['forum'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'community']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'community' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Topluluk</div>
                    <div class="mt-2 text-2xl font-black text-green-700">{{ $counts['community'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'moderation']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'moderation' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Moderasyon</div>
                    <div class="mt-2 text-2xl font-black text-yellow-700">{{ $counts['moderation'] }}</div>
                </a>
            </div>

            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('user.notifications') }}" class="grid gap-3 md:grid-cols-[1fr_1fr_auto]">
                    <select name="status" class="rounded-xl border-slate-300 text-sm">
                        <option value="">Tum okundu durumlari</option>
                        <option value="unread" @selected($selectedStatus === 'unread')>Okunmamis</option>
                        <option value="read" @selected($selectedStatus === 'read')>Okundu</option>
                    </select>

                    <select name="type" class="rounded-xl border-slate-300 text-sm">
                        <option value="">Tum bildirim tipleri</option>
                        @foreach($typeLabels as $type => $label)
                            <option value="{{ $type }}" @selected($selectedType === $type)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if($selectedGroup)
                        <input type="hidden" name="group" value="{{ $selectedGroup }}">
                    @endif

                    <button type="submit" class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700">
                        Filtrele
                    </button>
                </form>

                @if($selectedStatus || $selectedGroup || $selectedType)
                    <a href="{{ route('user.notifications') }}" class="mt-3 inline-flex text-sm font-black text-slate-500 transition hover:text-blue-700">
                        Filtreleri temizle
                    </a>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-black text-slate-950">
                        Bildirim Gecmisi
                    </h3>

                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-black text-blue-700">
                        {{ $notifications->total() }} bildirim
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($notifications as $notification)
                        <a href="{{ route('notifications.read', $notification) }}" class="block p-6 transition hover:bg-slate-50 {{ ! $notification->is_read ? 'bg-blue-50/60' : '' }}">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl font-black text-white {{ match($notification->group()) { 'forum' => 'bg-red-700', 'community' => 'bg-green-700', 'moderation' => 'bg-yellow-600', default => 'bg-blue-700' } }}">
                                    {{ match($notification->group()) { 'forum' => '#', 'community' => 'C', 'moderation' => 'M', default => '!' } }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-black text-slate-900">
                                                {{ $notification->title }}
                                            </h4>

                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-black text-slate-600">
                                                {{ $notification->typeLabel() }}
                                            </span>

                                            @if(! $notification->is_read)
                                                <span class="rounded-full bg-blue-700 px-2 py-1 text-xs font-black text-white">
                                                    Yeni
                                                </span>
                                            @endif
                                        </div>

                                        <span class="text-sm font-bold text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <p class="mt-3 leading-7 text-slate-600">
                                        {{ $notification->message }}
                                    </p>

                                    @if($notification->url)
                                        <div class="mt-3 text-sm font-black text-blue-700">
                                            Detaya git
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-12 text-center">
                            <h3 class="text-xl font-black text-slate-900">
                                Bildirim bulunmuyor
                            </h3>

                            <p class="mt-2 text-slate-500">
                                Secili filtreler icin bildirim yok.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="border-t border-slate-100 p-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
