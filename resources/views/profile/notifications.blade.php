<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="font-black text-2xl text-slate-900">
                    Bildirimler
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Hesabınıza gelen tüm bildirimleri buradan takip edebilirsiniz.
                </p>

            </div>

            <div class="flex items-center gap-3">

                <form
                    method="POST"
                    action="{{ route('user.notifications.readAll') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-800 transition"
                    >
                        Tümünü Okundu Yap
                    </button>

                </form>

                <a
                    href="/dashboard"
                    class="bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition"
                >
                    Panele Dön
                </a>

            </div>

        </div>

    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">

        <div class="max-w-5xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="border-b border-slate-100 px-6 py-5 flex items-center justify-between">

                    <h3 class="text-xl font-black text-slate-950">
                        Bildirim Geçmişi
                    </h3>

                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">

                        {{ $notifications->total() }} bildirim

                    </span>

                </div>

                <div class="divide-y divide-slate-100">

                    @forelse($notifications as $notification)

                        <a
                            href="{{ route('notifications.read', $notification) }}"
                            class="block p-6 hover:bg-slate-50 transition {{ ! $notification->is_read ? 'bg-blue-50/60' : '' }}"
                        >

                            <div class="flex items-start gap-4">

                                <div class="w-12 h-12 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-xl font-black shrink-0">

                                    🔔

                                </div>

                                <div class="flex-1">

                                    <div class="flex items-center justify-between gap-4 flex-wrap">

                                        <div class="flex items-center gap-3 flex-wrap">

                                            <h4 class="font-black text-slate-900">
                                                {{ $notification->title }}
                                            </h4>

                                            @if(! $notification->is_read)

                                                <span class="bg-blue-700 text-white px-2 py-1 rounded-full text-xs font-black">
                                                    Yeni
                                                </span>

                                            @endif

                                        </div>

                                        <span class="text-sm text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>

                                    </div>

                                    <p class="text-slate-600 leading-7 mt-3">
                                        {{ $notification->message }}
                                    </p>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="p-12 text-center">

                            <div class="text-5xl mb-4">
                                🔕
                            </div>

                            <h3 class="text-xl font-black text-slate-900">
                                Bildirim bulunmuyor
                            </h3>

                            <p class="text-slate-500 mt-2">
                                Yeni bildirimler burada görünecek.
                            </p>

                        </div>

                    @endforelse

                </div>

                @if($notifications->hasPages())

                    <div class="p-6 border-t border-slate-100">
                        {{ $notifications->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>