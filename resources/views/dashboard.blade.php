@extends('frontend.layout')

@section('title', 'Panelim')

@section('content')

@php
    $user = Auth::user();

    $roleLabel = $user->roleModel?->name ?? match($user->role) {
        'admin' => 'Yönetici',
        'editor' => 'Editör / Yazar',
        'moderator' => 'Moderatör',
        default => 'Kullanıcı',
    };

    $siteSetting = \App\Models\SiteSetting::first();

    $totalUsers = \App\Models\User::count();
    $totalNews = \App\Models\News::count();
    $totalAnnouncements = \App\Models\Announcement::count();

    $pendingComments = \App\Models\Comment::where('status', 'pending')->count();
    $approvedComments = \App\Models\Comment::where('status', 'approved')->count();
    $rejectedComments = \App\Models\Comment::where('status', 'rejected')->count();

    $activePunishments = \App\Models\UserPunishment::where('is_active', true)->count();

    $userComments = \App\Models\Comment::where('user_id', $user->id)->count();

    $userPendingComments = \App\Models\Comment::where('user_id', $user->id)
        ->where('status', 'pending')
        ->count();

    $unreadNotifications = \App\Models\Notification::where('user_id', $user->id)
        ->where('is_read', false)
        ->count();
@endphp

<section class="bg-slate-100 min-h-screen py-8">

    <div class="max-w-7xl mx-auto px-4">

        <div class="bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 rounded-2xl shadow-xl p-8 mb-8 text-white">

            <span class="inline-flex bg-blue-500/20 text-blue-200 border border-blue-400/30 px-3 py-1 rounded-full text-xs font-bold mb-4">
                {{ $siteSetting?->site_name ?? 'ilanhaber.net' }}
            </span>

            <h1 class="text-3xl md:text-4xl font-black">
                Hoş geldin, {{ $user->name }}
            </h1>

            <p class="text-slate-300 mt-3 max-w-2xl">
                @if($user->isAdmin() || $user->hasPermission('panel_giris'))
 
 @endif
            </p>

            <div class="mt-6 flex flex-wrap gap-3">

                <a href="/"
                   class="bg-white text-slate-900 px-4 py-2 rounded-xl text-sm font-black hover:bg-slate-100 transition">
                    Siteye Git
                </a>

                @if($user->isAdmin() || $user->hasPermission('panel_giris'))
                    <a href="/admin"
                       class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-black hover:bg-blue-700 transition">
                        Admin Panel
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}"
                   class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-black hover:bg-slate-700 transition">
                    Profil Ayarları
                </a>

            </div>

        </div>

        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

            @if($user->hasPermission('kullanici_yonet'))

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Toplam Kullanıcı</div>
                    <div class="text-4xl font-black mt-2">{{ $totalUsers }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Toplam Haber</div>
                    <div class="text-4xl font-black mt-2">{{ $totalNews }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Toplam İlan</div>
                    <div class="text-4xl font-black mt-2">{{ $totalAnnouncements }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Bekleyen Yorum</div>
                    <div class="text-4xl font-black text-orange-500 mt-2">{{ $pendingComments }}</div>
                </div>

            @elseif(
    $user->hasPermission('haber_ekle') ||
    $user->hasPermission('ilan_ekle')
 )

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Toplam Haber</div>
                    <div class="text-4xl font-black mt-2">{{ $totalNews }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Toplam İlan</div>
                    <div class="text-4xl font-black mt-2">{{ $totalAnnouncements }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Bekleyen Yorum</div>
                    <div class="text-4xl font-black text-orange-500 mt-2">{{ $pendingComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Aktif Cezalar</div>
                    <div class="text-4xl font-black text-slate-900 mt-2">{{ $activePunishments }}</div>
                </div>

            @elseif(
    $user->hasPermission('yorum_moderasyonu') ||
    $user->hasPermission('forum_moderasyonu')
 )

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Bekleyen Yorum</div>
                    <div class="text-4xl font-black text-orange-500 mt-2">{{ $pendingComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Onaylanan</div>
                    <div class="text-4xl font-black text-green-600 mt-2">{{ $approvedComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Reddedilen</div>
                    <div class="text-4xl font-black text-red-600 mt-2">{{ $rejectedComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Aktif Cezalar</div>
                    <div class="text-4xl font-black text-slate-900 mt-2">{{ $activePunishments }}</div>
                </div>

            @else

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Yorumlarım</div>
                    <div class="text-4xl font-black mt-2">{{ $userComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Onay Bekleyen</div>
                    <div class="text-4xl font-black text-orange-500 mt-2">{{ $userPendingComments }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Bildirimler</div>
                    <div class="text-4xl font-black text-blue-700 mt-2">{{ $unreadNotifications }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="text-sm text-slate-500 font-semibold">Durum</div>
                    <div class="text-2xl font-black text-green-600 mt-3">Aktif</div>
                </div>

            @endif

        </div>

        <div class="grid lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                    <div class="border-b border-slate-100 px-6 py-5">
                        <h3 class="text-xl font-black text-slate-950">
                            Hızlı İşlemler
                        </h3>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 p-6">

                        @if($user->hasPermission('kullanici_yonet'))

                            <a href="/admin"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">🛡️</div>
                                <h4 class="font-black">Admin Panel</h4>
                                <p class="text-sm text-slate-500 mt-2">Tüm sistemi yönetin.</p>
                            </a>

                            <a href="/admin/users"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">👥</div>
                                <h4 class="font-black">Kullanıcı Rolleri</h4>
                                <p class="text-sm text-slate-500 mt-2">Rol ve yetki yönetimi.</p>
                            </a>

                            <a href="/admin/comments"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">💬</div>
                                <h4 class="font-black">Yorum Moderasyonu</h4>
                                <p class="text-sm text-slate-500 mt-2">Bekleyen yorumları kontrol edin.</p>
                            </a>

                            <a href="/admin/advertisements"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">📢</div>
                                <h4 class="font-black">Reklam Yönetimi</h4>
                                <p class="text-sm text-slate-500 mt-2">Reklam alanlarını yönetin.</p>
                            </a>

                        @elseif(
    $user->hasPermission('haber_ekle') ||
    $user->hasPermission('ilan_ekle')
 )

                            <a href="/admin/news"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">📰</div>
                                <h4 class="font-black">Haber Yönetimi</h4>
                                <p class="text-sm text-slate-500 mt-2">Haber ekleyin ve düzenleyin.</p>
                            </a>

                            <a href="/admin/announcements"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">📢</div>
                                <h4 class="font-black">İlan Yönetimi</h4>
                                <p class="text-sm text-slate-500 mt-2">İlan ekleyin ve düzenleyin.</p>
                            </a>

                        @elseif(
    $user->hasPermission('yorum_moderasyonu') ||
    $user->hasPermission('forum_moderasyonu')
 )

                            <a href="/admin/comments"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">💬</div>
                                <h4 class="font-black">Bekleyen Yorumlar</h4>
                                <p class="text-sm text-slate-500 mt-2">Yorumları onaylayın veya reddedin.</p>
                            </a>

                            <a href="/admin/user-punishments"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">🚫</div>
                                <h4 class="font-black">Ceza Yönetimi</h4>
                                <p class="text-sm text-slate-500 mt-2">Kullanıcılara ceza verin.</p>
                            </a>

                        @else

                            <a href="{{ route('profile.edit') }}"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">👤</div>
                                <h4 class="font-black">Profil Düzenle</h4>
                                <p class="text-sm text-slate-500 mt-2">Kullanıcı bilgilerinizi güncelleyin.</p>
                            </a>

                            <a href="{{ route('user.comments') }}"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">💬</div>
                                <h4 class="font-black">Yorumlarım</h4>
                                <p class="text-sm text-slate-500 mt-2">Yaptığınız yorumları takip edin.</p>
                            </a>

                            <a href="{{ route('user.notifications') }}"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">🔔</div>
                                <h4 class="font-black">Bildirimler</h4>
                                <p class="text-sm text-slate-500 mt-2">Tüm bildirim geçmişinizi görüntüleyin.</p>
                            </a>

                            <a href="/"
                               class="group rounded-2xl border bg-slate-50 p-5 hover:bg-blue-50 transition">
                                <div class="text-3xl mb-3">🌐</div>
                                <h4 class="font-black">Siteye Dön</h4>
                                <p class="text-sm text-slate-500 mt-2">Ana sayfaya geri dönün.</p>
                            </a>

                        @endif

                    </div>

                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                    <div class="border-b border-slate-100 px-6 py-5">
                        <h3 class="text-xl font-black text-slate-950">
                            Sistem Durumu
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="rounded-2xl bg-slate-50 border border-dashed border-slate-300 p-8 text-center">
                            <div class="text-4xl mb-3">⚙️</div>

                            <h4 class="font-black text-slate-900">
                                Rol bazlı sistem aktif
                            </h4>

                            <p class="text-sm text-slate-500 mt-2">
                                Yorum, ceza, bildirim ve moderasyon sistemleri aktif çalışıyor.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

            <div class="space-y-8">

                <div id="bildirimler" class="scroll-mt-32 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                    <div class="border-b border-slate-100 px-6 py-5 flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-950">
                            Bildirimler
                        </h3>

                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-black">
                            {{ $unreadNotifications }} okunmamış
                        </span>
                    </div>

                    <div class="divide-y divide-slate-100">

                        @forelse($user->notifications()->take(5)->get() as $notification)

                            <a href="{{ route('notifications.read', $notification) }}"
                               class="block p-5 hover:bg-slate-50 transition {{ ! $notification->is_read ? 'bg-blue-50/60' : '' }}">

                                <div class="flex items-start gap-3">

                                    <div class="w-10 h-10 rounded-xl bg-blue-700 text-white flex items-center justify-center font-black shrink-0">
                                        🔔
                                    </div>

                                    <div class="flex-1">

                                        <div class="flex items-center justify-between gap-3">
                                            <p class="font-black text-slate-900">
                                                {{ $notification->title }}
                                            </p>

                                            @if(! $notification->is_read)
                                                <span class="text-xs bg-blue-700 text-white px-2 py-1 rounded-full font-bold">
                                                    Yeni
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-slate-600 mt-1 leading-6">
                                            {{ $notification->message }}
                                        </p>

                                        <p class="text-xs text-slate-400 mt-2">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>

                                    </div>

                                </div>

                            </a>

                        @empty

                            <div class="p-8 text-center">
                                <div class="text-4xl mb-3">🔕</div>

                                <h4 class="font-black text-slate-900">
                                    Henüz bildirim yok
                                </h4>

                                <p class="text-sm text-slate-500 mt-2">
                                    Yorum onayı, red ve ceza bildirimleri burada görünecek.
                                </p>
                            </div>

                        @endforelse

                    </div>

                    <div class="border-t border-slate-100 p-4">
                        <a href="{{ route('user.notifications') }}"
                           class="block text-center text-sm font-black text-blue-700 hover:underline">
                            Tüm Bildirimleri Gör
                        </a>
                    </div>

                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

                    <div class="flex items-center gap-4">

                        <div class="w-16 h-16 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-2xl font-black">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>

                        <div>
                            <h3 class="font-black text-slate-950 text-lg">
                                {{ $user->name }}
                            </h3>

                            <p class="text-sm text-slate-500">
                                {{ $user->email }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-6 space-y-3 text-sm">

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Üyelik Durumu</span>
                            <span class="font-bold text-green-600">Aktif</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Rol</span>
                            <span class="font-bold text-slate-900">{{ $roleLabel }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Kayıt Tarihi</span>
                            <span class="font-bold text-slate-900">
                                {{ $user->created_at->format('d.m.Y') }}
                            </span>
                        </div>

                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="mt-6 block text-center bg-slate-900 text-white px-4 py-3 rounded-xl font-black text-sm hover:bg-slate-800 transition">
                        Profilimi Düzenle
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection