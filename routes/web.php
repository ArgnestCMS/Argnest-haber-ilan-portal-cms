<?php
use App\Http\Controllers\WorkSessionController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\UserCommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontendController::class, 'home']);

Route::get('/haberler', [FrontendController::class, 'news']);

Route::get('/ilanlar', [FrontendController::class, 'announcements']);

Route::get('/haber/{slug}', [FrontendController::class, 'newsDetail']);

Route::get('/ilan/{slug}', [FrontendController::class, 'announcementDetail']);

Route::get('/kategori/{slug}', [FrontendController::class, 'category']);
Route::get('/arama', [FrontendController::class, 'search']);
Route::get('/videolar', [FrontendController::class, 'videos'])
    ->name('videos.index');

Route::get('/video/{slug}', [FrontendController::class, 'videoDetail'])
    ->name('videos.show');

Route::get('/galeriler', [FrontendController::class, 'galleries'])
    ->name('galleries.index');

Route::get('/galeri/{slug}', [FrontendController::class, 'galleryDetail'])
    ->name('galleries.show');
    Route::post('/yorum/video/{video}', [CommentController::class, 'storeVideo'])
    ->name('comments.video.store');

Route::post('/yorum/galeri/{gallery}', [CommentController::class, 'storeGallery'])
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
    ->name('user.notifications');

Route::post('/bildirimler/okundu-yap', [UserNotificationController::class, 'markAllAsRead'])
    ->name('user.notifications.readAll');
Route::get('/yorumlarim', [UserCommentController::class, 'index'])
    ->name('user.comments');
Route::get('/bildirim/{notification}', [NotificationController::class, 'read'])
    ->name('notifications.read');
Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/bildirimler/count', [UserNotificationController::class, 'unreadCount'])
    ->name('user.notifications.count');

Route::get('/bildirimler/latest', [UserNotificationController::class, 'latest'])
    ->name('user.notifications.latest');
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    Route::post('/yorum/haber/{news}', [CommentController::class, 'storeNews'])
        ->name('comments.news.store');

    Route::post('/yorum/ilan/{announcement}', [CommentController::class, 'storeAnnouncement'])
        ->name('comments.announcement.store');

});

/*
|--------------------------------------------------------------------------
| SEO Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/robots.txt', [SitemapController::class, 'robots']);

/*
|--------------------------------------------------------------------------
| Breeze Auth
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';