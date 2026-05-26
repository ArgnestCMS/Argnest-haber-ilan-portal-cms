<?php
use App\Http\Controllers\WorkSessionController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\UserCommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumDashboardController;
use App\Http\Controllers\LiveActivityController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommunityReportController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\SocialPresenceController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ContentAttachmentController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\AdminDatabaseBackupDownloadController;
use App\Http\Controllers\AdminSiteReportExportController;
use App\Http\Controllers\InstallController;
use App\Http\Middleware\EnsureAdminPanelAccess;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/install', [InstallController::class, 'show'])
    ->middleware('throttle:120,1')
    ->name('install');

Route::post('/install', [InstallController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('install.store');

Route::get('/', [FrontendController::class, 'home']);

Route::get('/health', HealthCheckController::class)
    ->middleware('throttle:30,1')
    ->name('health');

Route::get('/haberler', [FrontendController::class, 'news']);

Route::get('/ilanlar', [FrontendController::class, 'announcements']);

Route::get('/haber/{slug}', [FrontendController::class, 'newsDetail']);

Route::get('/ilan/{slug}', [FrontendController::class, 'announcementDetail']);

Route::get('/kategori/{slug}', [FrontendController::class, 'category']);
Route::get('/arama/instant', [SearchController::class, 'instant'])
    ->middleware('throttle:60,1')
    ->name('search.instant');
Route::get('/arama', [SearchController::class, 'index'])
    ->name('search');
Route::get('/videolar', [FrontendController::class, 'videos'])
    ->name('videos.index');

Route::get('/video/{slug}', [FrontendController::class, 'videoDetail'])
    ->name('videos.show');

Route::get('/galeriler', [FrontendController::class, 'galleries'])
    ->name('galleries.index');

Route::get('/galeri/{slug}', [FrontendController::class, 'galleryDetail'])
    ->name('galleries.show');

Route::get('/anketler', [PollController::class, 'index'])
    ->name('polls.index');

Route::get('/anket/{slug}', [PollController::class, 'show'])
    ->name('polls.show');

Route::post('/anket/{poll}/oy', [PollController::class, 'vote'])
    ->middleware('throttle:10,1')
    ->name('polls.vote');

Route::get('/forum', [FrontendController::class, 'forum'])
    ->name('forum.index');

Route::get('/forum/kategori/{slug}', [FrontendController::class, 'forumCategory'])
    ->name('forum.categories.show');

Route::get('/forum/etiket/{slug}', [FrontendController::class, 'forumTag'])
    ->name('forum.tags.show');

Route::get('/forum/konu/{topic}/aktif-kullanicilar', [SocialPresenceController::class, 'topicUsers'])
    ->name('forum.topics.presence');

Route::get('/forum/konu/{slug}', [FrontendController::class, 'forumTopic'])
    ->name('forum.topics.show');

Route::get('/canli-aktivite', [FrontendController::class, 'liveActivity'])
    ->name('live-activity.index');

Route::get('/canli-aktivite/latest', [LiveActivityController::class, 'latest'])
    ->middleware('throttle:120,1')
    ->name('live-activity.latest');

Route::get('/canli-sohbet', [FrontendController::class, 'liveChat'])
    ->name('live-chat.index');

Route::get('/canli-sohbet/mesajlar', [LiveChatController::class, 'messages'])
    ->middleware('throttle:120,1')
    ->name('live-chat.messages');

Route::get('/canli-sohbet/online', [LiveChatController::class, 'onlineUsers'])
    ->middleware('throttle:120,1')
    ->name('live-chat.online');

Route::get('/presence/user/{user}', [SocialPresenceController::class, 'user'])
    ->name('presence.user');

Route::get('/presence/users', [SocialPresenceController::class, 'users'])
    ->name('presence.users');

Route::get('/canli-sohbet/typing', [SocialPresenceController::class, 'liveChatTypingUsers'])
    ->middleware('throttle:120,1')
    ->name('live-chat.typing');

Route::post('/yorum/video/{video}', [CommentController::class, 'storeVideo'])
    ->middleware('throttle:6,1')
    ->name('comments.video.store');

Route::post('/yorum/galeri/{gallery}', [CommentController::class, 'storeGallery'])
    ->middleware('throttle:6,1')
    ->name('comments.gallery.store');
/*
|--------------------------------------------------------------------------
| Public Profile
|--------------------------------------------------------------------------
*/

Route::get('/profil/{user}', [ProfileController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {

    return view('dashboard');

})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/mesai/baslat', [WorkSessionController::class, 'startWork'])
    ->name('work.start');

Route::post('/mesai/mola', [WorkSessionController::class, 'startBreak'])
    ->name('work.break');

Route::post('/mesai/yemek', [WorkSessionController::class, 'startLunch'])
    ->name('work.lunch');

Route::post('/mesai/aktif', [WorkSessionController::class, 'backToWork'])
    ->name('work.active');

Route::post('/mesai/bitir', [WorkSessionController::class, 'endWork'])
    ->name('work.end');
Route::get('/bildirimler', [UserNotificationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('user.notifications');

Route::post('/bildirimler/okundu-yap', [UserNotificationController::class, 'markAllAsRead'])
    ->middleware(['auth', 'verified'])
    ->name('user.notifications.readAll');
Route::get('/yorumlarim', [UserCommentController::class, 'index'])
    ->name('user.comments');
Route::get('/bildirim/{notification}', [NotificationController::class, 'read'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.read');
Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/bildirimler/count', [UserNotificationController::class, 'unreadCount'])
    ->name('user.notifications.count');

    Route::get('/bildirimler/latest', [UserNotificationController::class, 'latest'])
    ->name('user.notifications.latest');

    Route::post('/presence/heartbeat', [SocialPresenceController::class, 'heartbeat'])
        ->middleware('throttle:90,1')
        ->name('presence.heartbeat');

    Route::post('/forum/konu/{topic}/aktif-kullanicilar', [SocialPresenceController::class, 'touchTopic'])
        ->middleware('throttle:90,1')
        ->name('forum.topics.presence.touch');

    Route::post('/canli-sohbet/typing', [SocialPresenceController::class, 'liveChatTyping'])
        ->middleware('throttle:60,1')
        ->name('live-chat.typing.store');

    Route::get('/mesajlar', [PrivateMessageController::class, 'index'])
        ->name('messages.index');

    Route::get('/mesajlar/count', [PrivateMessageController::class, 'count'])
        ->middleware('throttle:120,1')
        ->name('messages.count');

    Route::get('/mesajlar/{conversation}', [PrivateMessageController::class, 'show'])
        ->name('messages.show');

    Route::get('/mesajlar/{conversation}/latest', [PrivateMessageController::class, 'latest'])
        ->middleware('throttle:120,1')
        ->name('messages.latest');

    Route::get('/mesajlar/{conversation}/typing', [PrivateMessageController::class, 'typingUsers'])
        ->middleware('throttle:120,1')
        ->name('messages.typing');

    Route::post('/mesajlar/{conversation}/mesaj', [PrivateMessageController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('messages.store');

    Route::post('/mesajlar/{conversation}/typing', [PrivateMessageController::class, 'typing'])
        ->middleware('throttle:60,1')
        ->name('messages.typing.store');

    Route::patch('/mesajlar/{conversation}/mesaj/{message}', [PrivateMessageController::class, 'edit'])
        ->middleware('throttle:30,1')
        ->name('messages.edit');

    Route::delete('/mesajlar/{conversation}/mesaj/{message}', [PrivateMessageController::class, 'destroy'])
        ->middleware('throttle:30,1')
        ->name('messages.destroy');

    Route::post('/mesajlar/{conversation}/mesaj/{message}/reaction', [PrivateMessageController::class, 'react'])
        ->middleware('throttle:60,1')
        ->name('messages.reactions.toggle');

    Route::post('/mesajlar/{conversation}/kabul', [PrivateMessageController::class, 'accept'])
        ->middleware('throttle:20,1')
        ->name('messages.accept');

    Route::post('/mesajlar/{conversation}/reddet', [PrivateMessageController::class, 'reject'])
        ->middleware('throttle:20,1')
        ->name('messages.reject');

    Route::post('/mesajlar/{conversation}/okundu', [PrivateMessageController::class, 'markRead'])
        ->middleware('throttle:120,1')
        ->name('messages.read');

    Route::post('/mesajlar/{conversation}/sessiz', [PrivateMessageController::class, 'toggleMute'])
        ->middleware('throttle:30,1')
        ->name('messages.mute');

    Route::post('/mesajlar/{conversation}/sabitle', [PrivateMessageController::class, 'togglePin'])
        ->middleware('throttle:30,1')
        ->name('messages.pin');

    Route::post('/profil/{user}/mesaj', [PrivateMessageController::class, 'start'])
        ->middleware('throttle:10,1')
        ->name('messages.start');

    Route::post('/profil/{user}/mesaj-engelle', [PrivateMessageController::class, 'block'])
        ->middleware('throttle:20,1')
        ->name('messages.block');

    Route::delete('/profil/{user}/mesaj-engelle', [PrivateMessageController::class, 'unblock'])
        ->middleware('throttle:20,1')
        ->name('messages.unblock');

    Route::patch('/mesaj-ayarlar', [PrivateMessageController::class, 'updateSettings'])
        ->middleware('throttle:10,1')
        ->name('messages.settings.update');

    Route::get('/push/config', [PushSubscriptionController::class, 'config'])
        ->middleware('throttle:60,1')
        ->name('push.config');

    Route::post('/push/subscriptions', [PushSubscriptionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('push.subscriptions.store');

    Route::patch('/push/subscriptions/preferences', [PushSubscriptionController::class, 'updatePreferences'])
        ->middleware('throttle:20,1')
        ->name('push.subscriptions.preferences');

    Route::delete('/push/subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->middleware('throttle:20,1')
        ->name('push.subscriptions.destroy');
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->middleware('throttle:5,1')
        ->name('profile.destroy');

    Route::post('/profil/{user}/takip', [UserFollowController::class, 'toggle'])
        ->middleware('throttle:30,1')
        ->name('users.follow.toggle');

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    Route::post('/yorum/haber/{news}', [CommentController::class, 'storeNews'])
        ->middleware('throttle:6,1')
        ->name('comments.news.store');

    Route::post('/yorum/ilan/{announcement}', [CommentController::class, 'storeAnnouncement'])
        ->middleware('throttle:6,1')
        ->name('comments.announcement.store');

    Route::post('/forum/konu', [ForumController::class, 'storeTopic'])
        ->middleware('throttle:6,1')
        ->name('forum.topics.store');

    Route::post('/forum/ai-assistant', [ForumController::class, 'assistant'])
        ->middleware('throttle:12,1')
        ->name('forum.assistant');

    Route::post('/forum/gorsel', [ForumController::class, 'uploadImage'])
        ->middleware('throttle:20,1')
        ->name('forum.images.store');

    Route::post('/forum/konu/{topic}/cevap', [ForumController::class, 'storePost'])
        ->middleware('throttle:10,1')
        ->name('forum.posts.store');

    Route::post('/forum/konu/{topic}/begeni', [ForumController::class, 'toggleLike'])
        ->middleware('throttle:60,1')
        ->name('forum.topics.like');

    Route::post('/forum/konu/{topic}/favori', [ForumController::class, 'toggleBookmark'])
        ->middleware('throttle:60,1')
        ->name('forum.topics.bookmark');

    Route::post('/rapor/forum-konu/{topic}', [CommunityReportController::class, 'reportTopic'])
        ->middleware('throttle:5,1')
        ->name('reports.forum-topics.store');

    Route::post('/rapor/forum-cevap/{post}', [CommunityReportController::class, 'reportPost'])
        ->middleware('throttle:5,1')
        ->name('reports.forum-posts.store');

    Route::get('/forum/panelim', [ForumDashboardController::class, 'index'])
        ->name('forum.dashboard');

    Route::post('/canli-sohbet/mesaj', [LiveChatController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('live-chat.messages.store');

    Route::post('/rapor/canli-sohbet/{message}', [CommunityReportController::class, 'reportLiveChatMessage'])
        ->middleware('throttle:5,1')
        ->name('reports.live-chat-messages.store');

});

/*
|--------------------------------------------------------------------------
| SEO Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemap-news.xml/{page?}', [SitemapController::class, 'news'])->whereNumber('page');
Route::get('/sitemap-announcements.xml/{page?}', [SitemapController::class, 'announcements'])->whereNumber('page');
Route::get('/sitemap-forum.xml/{page?}', [SitemapController::class, 'forum'])->whereNumber('page');
Route::get('/sitemap-categories.xml/{page?}', [SitemapController::class, 'categories'])->whereNumber('page');
Route::get('/sitemap-media.xml/{page?}', [SitemapController::class, 'media'])->whereNumber('page');

Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::delete('/admin/content-attachments/{mediaAsset}', [ContentAttachmentController::class, 'destroy'])
    ->middleware(['auth', EnsureAdminPanelAccess::class])
    ->name('admin.content-attachments.destroy');

Route::patch('/admin/content-attachments/{mediaAsset}', [ContentAttachmentController::class, 'update'])
    ->middleware(['auth', EnsureAdminPanelAccess::class])
    ->name('admin.content-attachments.update');

Route::get('/admin/database-backups/download/{filename}', AdminDatabaseBackupDownloadController::class)
    ->middleware(['auth', EnsureAdminPanelAccess::class])
    ->where('filename', '[^/]+')
    ->name('admin.database-backups.download');

Route::get('/admin/site-reports/export/{format}', AdminSiteReportExportController::class)
    ->middleware(['auth', EnsureAdminPanelAccess::class])
    ->whereIn('format', ['csv', 'xlsx'])
    ->name('admin.site-reports.export');

/*
|--------------------------------------------------------------------------
| Breeze Auth
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
