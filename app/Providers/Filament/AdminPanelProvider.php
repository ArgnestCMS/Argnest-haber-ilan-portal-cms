<?php

namespace App\Providers\Filament;
use App\Filament\Widgets\OnlineUsersWidget;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use App\Filament\Widgets\NotificationCenterWidget;
use App\Filament\Widgets\ContentCreationChartWidget;
use App\Filament\Widgets\SecurityLoginChartWidget;
use App\Filament\Widgets\AdminWelcomeWidget;
use App\Filament\Widgets\PendingCommentsWidget;
use App\Filament\Widgets\ModerationStatsWidget;
use App\Filament\Widgets\SecurityAlertWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\SecurityStatsWidget;
use App\Filament\Widgets\CurrentStaffStatus;
use App\Http\Middleware\EnsureAdminPanelAccess;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])

             ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

             ->resources([
                \App\Filament\Resources\News\NewsResource::class,
                \App\Filament\Resources\Announcements\AnnouncementResource::class,
                \App\Filament\Resources\Comments\CommentResource::class,
                \App\Filament\Resources\UserPunishments\UserPunishmentResource::class,
            ])

             ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

             ->pages([
                Dashboard::class,
            ])

             ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

             ->widgets([
    AdminWelcomeWidget::class,
    SecurityStatsWidget::class,
    ModerationStatsWidget::class,
    CurrentStaffStatus::class,
    RecentActivityWidget::class,
    SecurityAlertWidget::class,
    PendingCommentsWidget::class,
    SecurityLoginChartWidget::class,
    ContentCreationChartWidget::class,
    NotificationCenterWidget::class,
    OnlineUsersWidget::class,
])
->renderHook(
    PanelsRenderHook::USER_MENU_BEFORE,
    fn (): string => Blade::render('@include("filament.notification-bell")')
)

             ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

             ->authMiddleware([
             Authenticate::class,
             EnsureAdminPanelAccess::class,
       ]);
    }
}