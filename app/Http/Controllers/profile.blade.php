@extends('frontend.layout')

@section('title', $user->name . ' Profili')

@section('content')

<section class="max-w-6xl mx-auto px-4 mt-8">

    <div class="bg-white shadow rounded-3xl overflow-hidden">

        <div class="bg-gradient-to-r from-blue-700 to-slate-900 h-48"></div>

        <div class="p-8">

            <div class="-mt-24 flex flex-col md:flex-row md:items-end gap-6">

                <div class="w-40 h-40 rounded-3xl overflow-hidden border-8 border-white shadow-xl bg-slate-100">

                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl font-black text-slate-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                </div>

                <div class="pb-4">
                    <h1 class="text-4xl font-black text-slate-900">
                        {{ $user->name }}
                    </h1>

                    <p class="text-slate-500 mt-2">
                        {{ $user->email }}
                    </p>
                </div>

            </div>

            @if($user->bio)
                <div class="mt-8 bg-slate-50 border border-slate-200 rounded-2xl p-6">
                    <h2 class="font-black text-xl mb-4">
                        Hakkında
                    </h2>

                    <div class="text-slate-700 leading-8">
                        {{ $user->bio }}
                    </div>
                </div>
            @endif

            <div class="grid md:grid-cols-4 gap-6 mt-8">

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Toplam Haber</div>
                    <div class="text-4xl font-black mt-2">0</div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Toplam İlan</div>
                    <div class="text-4xl font-black mt-2">0</div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Görüntülenme</div>
                    <div class="text-4xl font-black mt-2">0</div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Üyelik</div>
                    <div class="text-2xl font-black mt-2 text-green-600">Aktif</div>
                </div>

            </div>

        </div>

    </div>

</section>

@endsection