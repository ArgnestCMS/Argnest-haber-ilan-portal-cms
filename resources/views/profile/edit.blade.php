<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">
                    Profil Ayarları
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Avatar, biyografi ve sosyal medya bilgilerinizi buradan düzenleyin.
                </p>
            </div>

            <a href="/profil/{{ auth()->id() }}"
               class="hidden sm:inline-flex bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition">
                Profilimi Gör
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid lg:grid-cols-3 gap-8">

                {{-- SOL PROFİL ÖNİZLEME --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-8">

                        <div class="h-32 bg-gradient-to-r from-blue-700 to-slate-900"></div>

                        <div class="p-6">

                            <div class="-mt-20 mb-5">
                                <div class="w-28 h-28 rounded-3xl overflow-hidden border-8 border-white shadow-xl bg-slate-100">

                                    @if(auth()->user()->avatar)
                                        <img
                                            src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                            class="w-full h-full object-cover"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-5xl font-black text-slate-400">
                                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif

                                </div>
                            </div>

                            <h3 class="text-2xl font-black text-slate-950">
                                {{ auth()->user()->name }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ auth()->user()->email }}
                            </p>

                            @if(auth()->user()->bio)
                                <p class="text-sm text-slate-600 mt-4 leading-6">
                                    {{ auth()->user()->bio }}
                                </p>
                            @else
                                <p class="text-sm text-slate-400 mt-4 leading-6">
                                    Henüz biyografi eklenmedi.
                                </p>
                            @endif

                            <a href="/profil/{{ auth()->id() }}"
                               class="mt-6 block text-center bg-slate-900 text-white px-4 py-3 rounded-xl font-black text-sm hover:bg-slate-800 transition">
                                Public Profili Aç
                            </a>

                        </div>

                    </div>
                </div>

                {{-- SAĞ AYARLAR --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- PROFİL BİLGİLERİ --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-xl font-black text-slate-950">
                                Profil Bilgileri
                            </h3>
                        </div>

                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- ŞİFRE --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="border-b border-slate-100 px-6 py-5">
                            <h3 class="text-xl font-black text-slate-950">
                                Şifre Güncelle
                            </h3>
                        </div>

                        <div class="p-6 max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- HESAP SİL --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                        <div class="border-b border-red-100 px-6 py-5 bg-red-50">
                            <h3 class="text-xl font-black text-red-700">
                                Hesabı Sil
                            </h3>
                        </div>

                        <div class="p-6 max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>