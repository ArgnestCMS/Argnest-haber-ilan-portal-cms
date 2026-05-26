<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-black text-2xl text-slate-900">
                    Yorumlarım
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Yapmış olduğunuz yorumları buradan takip edebilirsiniz.
                </p>
            </div>

            <a href="/dashboard"
               class="bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-800 transition">
                Panele Dön
            </a>

        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">

        <div class="max-w-5xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="border-b border-slate-100 px-6 py-5 flex items-center justify-between">

                    <h3 class="text-xl font-black text-slate-950">
                        Toplam Yorum
                    </h3>

                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">
                        {{ $comments->total() }} yorum
                    </span>

                </div>

                <div class="divide-y divide-slate-100">

                    @forelse($comments as $comment)

                        <div class="p-6">

                            <div class="flex items-start justify-between gap-4">

                                <div class="flex-1">

                                    <div class="flex items-center gap-3 flex-wrap">

                                        @if($comment->commentable_type === 'App\Models\News')

                                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">
                                                Haber Yorumu
                                            </span>

                                        @else

                                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-black">
                                                İlan Yorumu
                                            </span>

                                        @endif

                                        @if($comment->status === 'approved')

                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black">
                                                Onaylandı
                                            </span>

                                        @elseif($comment->status === 'pending')

                                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-black">
                                                Beklemede
                                            </span>

                                        @else

                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-black">
                                                Reddedildi
                                            </span>

                                        @endif

                                    </div>

                                    <p class="text-slate-800 leading-7 mt-4">
                                        {{ $comment->content }}
                                    </p>

                                    <div class="flex items-center gap-5 mt-4 text-sm text-slate-500">

                                        <span>
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>

                                        @if($comment->commentable)

                                            @php
                                                $url = $comment->commentable_type === 'App\Models\News'
                                                    ? '/haber/' . $comment->commentable->slug
                                                    : '/ilan/' . $comment->commentable->slug;
                                            @endphp

                                            <a href="{{ $url }}"
                                               class="text-blue-700 font-bold hover:underline">
                                                İçeriğe Git
                                            </a>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="p-12 text-center">

                            <div class="text-5xl mb-4">
                                💬
                            </div>

                            <h3 class="text-xl font-black text-slate-900">
                                Henüz yorum yapmadınız
                            </h3>

                            <p class="text-slate-500 mt-2">
                                Haber ve ilanlara yorum yapmaya başlayabilirsiniz.
                            </p>

                        </div>

                    @endforelse

                </div>

                @if($comments->hasPages())

                    <div class="p-6 border-t border-slate-100">
                        {{ $comments->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>