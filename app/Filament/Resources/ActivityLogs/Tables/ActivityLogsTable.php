<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Kullanıcı')->searchable()->sortable()->placeholder('Sistem'),

                TextColumn::make('action')
                    ->label('İşlem')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'login' => 'Giriş',
                        'logout' => 'Çıkış',
                        'failed_login' => 'Başarısız Giriş',
                        'suspicious_login' => 'Şüpheli Giriş',
                        'suspicious_device_login' => 'Farklı Cihaz Girişi',

                        'comment_approved' => 'Yorum Onaylandı',
                        'comment_rejected' => 'Yorum Reddedildi',
                        'comment_submitted' => 'Yorum Gönderildi',
                        'blocked_comment_attempt' => 'Engelli Yorum Denemesi',
                        'flood_comment_detected' => 'Flood Tespit Edildi',
                        'spam_comment_rejected' => 'Spam Yorum Reddedildi',
                        'auto_punishment_applied' => 'Otomatik Ceza Uygulandı',
                        'punishment_given' => 'Ceza Verildi',
                        'edit_punishment' => 'Ceza Düzenlendi',
                        'delete_punishment' => 'Ceza Silindi',

                        'create_news' => 'Haber Eklendi',
                        'edit_news' => 'Haber Düzenlendi',
                        'delete_news' => 'Haber Silindi',

                        'create_announcement' => 'İlan Eklendi',
                        'edit_announcement' => 'İlan Düzenlendi',
                        'delete_announcement' => 'İlan Silindi',

                        'create_advertisement' => 'Reklam Eklendi',
                        'edit_advertisement' => 'Reklam Düzenlendi',
                        'delete_advertisement' => 'Reklam Silindi',

                        'create_category' => 'Kategori Eklendi',
                        'edit_category' => 'Kategori Düzenlendi',
                        'delete_category' => 'Kategori Silindi',

                        'create_user' => 'Kullanıcı Eklendi',
                        'edit_user' => 'Kullanıcı Düzenlendi',
                        'update_user' => 'Kullanıcı Güncellendi',
                        'delete_user' => 'Kullanıcı Silindi',
                        'ban_user' => 'Kullanıcı Banlandı',

                        'edit_site_setting' => 'Site Ayarı Güncellendi',
                        'delete_site_setting' => 'Site Ayarı Silindi',
                        'maintenance_mode_enabled' => 'Bakım Modu Açıldı',
                        'maintenance_mode_disabled' => 'Bakım Modu Kapatıldı',

                        'create_work_session' => 'Mesai Kaydı Eklendi',
                        'edit_work_session' => 'Mesai Kaydı Düzenlendi',
                        'delete_work_session' => 'Mesai Kaydı Silindi',

                        default => $state ?? 'Bilinmiyor',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'login',
                        'create_news',
                        'create_announcement',
                        'create_advertisement',
                        'create_category',
                        'create_user',
                        'create_work_session',
                        'comment_approved',
                        'comment_submitted',
                        'maintenance_mode_disabled' => 'success',

                        'logout' => 'gray',

                        'edit_news',
                        'edit_announcement',
                        'edit_advertisement',
                        'edit_category',
                        'edit_user',
                        'update_user',
                        'edit_site_setting',
                        'edit_punishment',
                        'edit_work_session',
                        'punishment_given',
                        'blocked_comment_attempt',
                        'flood_comment_detected',
                        'suspicious_device_login',
                        'maintenance_mode_enabled' => 'warning',

                        'delete_news',
                        'delete_announcement',
                        'delete_advertisement',
                        'delete_category',
                        'delete_user',
                        'delete_site_setting',
                        'delete_punishment',
                        'delete_work_session',
                        'ban_user',
                        'comment_rejected',
                        'spam_comment_rejected',
                        'auto_punishment_applied',
                        'failed_login',
                        'suspicious_login' => 'danger',

                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'login' => 'heroicon-o-arrow-right-end-on-rectangle',
                        'logout' => 'heroicon-o-arrow-left-start-on-rectangle',

                        'failed_login' => 'heroicon-o-exclamation-triangle',
                        'suspicious_login' => 'heroicon-o-shield-exclamation',
                        'suspicious_device_login' => 'heroicon-o-computer-desktop',

                        'comment_approved' => 'heroicon-o-check-circle',
                        'comment_rejected' => 'heroicon-o-x-circle',
                        'comment_submitted' => 'heroicon-o-chat-bubble-left-right',

                        'blocked_comment_attempt' => 'heroicon-o-no-symbol',

                        'flood_comment_detected' => 'heroicon-o-exclamation-triangle',

                        'spam_comment_rejected' => 'heroicon-o-shield-exclamation',
                        'punishment_given',
                        'edit_punishment',
                        'delete_punishment' => 'heroicon-o-shield-exclamation',
                        'auto_punishment_applied' => 'heroicon-o-shield-exclamation',

                        'create_news',
                        'create_announcement',
                        'create_advertisement',
                        'create_category',
                        'create_user',
                        'create_work_session' => 'heroicon-o-plus-circle',

                        'edit_news',
                        'edit_announcement',
                        'edit_advertisement',
                        'edit_category',
                        'edit_user',
                        'update_user',
                        'edit_site_setting',
                        'edit_work_session' => 'heroicon-o-pencil-square',

                        'delete_news',
                        'delete_announcement',
                        'delete_advertisement',
                        'delete_category',
                        'delete_user',
                        'delete_site_setting',
                        'delete_work_session' => 'heroicon-o-trash',

                        'maintenance_mode_enabled',
                        'maintenance_mode_disabled' => 'heroicon-o-cog-6-tooth',

                        'ban_user' => 'heroicon-o-no-symbol',

                        default => 'heroicon-o-information-circle',
                    })
                    ->searchable(),

                TextColumn::make('description')->label('Açıklama')->limit(70)->searchable()->wrap(),
                TextColumn::make('properties.title')->label('İçerik')->limit(45)->searchable()->placeholder('-'),
                TextColumn::make('ip_address')->label('IP')->searchable()->copyable()->placeholder('-'),

                TextColumn::make('device')->label('Cihaz')->badge()->color(fn (?string $state): string => match ($state) {
                    'Mobile' => 'success',
                    'Desktop' => 'info',
                    default => 'gray',
                })->placeholder('-'),

                TextColumn::make('browser')->label('Tarayıcı')->badge()->color(fn (?string $state): string => match ($state) {
                    'Chrome' => 'success',
                    'Opera' => 'danger',
                    'Firefox' => 'warning',
                    'Edge' => 'info',
                    'Safari' => 'gray',
                    default => 'gray',
                })->placeholder('-'),

                TextColumn::make('platform')->label('Platform')->badge()->color(fn (?string $state): string => match ($state) {
                    'Windows' => 'info',
                    'Android' => 'success',
                    'iOS' => 'gray',
                    'MacOS' => 'warning',
                    'Linux' => 'danger',
                    default => 'gray',
                })->placeholder('-'),

                TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i:s')->sortable(),
            ])

            ->filters([
                SelectFilter::make('action')
                    ->label('İşlem Tipi')
                    ->options([
                        'login' => 'Giriş',
                        'logout' => 'Çıkış',
                        'failed_login' => 'Başarısız Giriş',
                        'suspicious_login' => 'Şüpheli Giriş',
                        'suspicious_device_login' => 'Farklı Cihaz Girişi',

                        'comment_approved' => 'Yorum Onaylandı',
                        'comment_rejected' => 'Yorum Reddedildi',
                        'auto_punishment_applied' => 'Otomatik Ceza Uygulandı',
                        'comment_submitted' => 'Yorum Gönderildi',
                        'blocked_comment_attempt' => 'Engelli Yorum Denemesi',
                        'flood_comment_detected' => 'Flood Tespit Edildi',
                        'spam_comment_rejected' => 'Spam Yorum Reddedildi',
                        'punishment_given' => 'Ceza Verildi',
                        'edit_punishment' => 'Ceza Düzenlendi',
                        'delete_punishment' => 'Ceza Silindi',

                        'create_news' => 'Haber Ekleme',
                        'edit_news' => 'Haber Düzenleme',
                        'delete_news' => 'Haber Silme',

                        'create_announcement' => 'İlan Ekleme',
                        'edit_announcement' => 'İlan Düzenleme',
                        'delete_announcement' => 'İlan Silme',

                        'create_advertisement' => 'Reklam Ekleme',
                        'edit_advertisement' => 'Reklam Düzenleme',
                        'delete_advertisement' => 'Reklam Silme',

                        'create_category' => 'Kategori Ekleme',
                        'edit_category' => 'Kategori Düzenleme',
                        'delete_category' => 'Kategori Silme',

                        'create_user' => 'Kullanıcı Ekleme',
                        'edit_user' => 'Kullanıcı Düzenleme',
                        'update_user' => 'Kullanıcı Güncelleme',
                        'delete_user' => 'Kullanıcı Silme',
                        'ban_user' => 'Ban İşlemi',

                        'edit_site_setting' => 'Site Ayarı Güncelleme',
                        'delete_site_setting' => 'Site Ayarı Silme',
                        'maintenance_mode_enabled' => 'Bakım Modu Açma',
                        'maintenance_mode_disabled' => 'Bakım Modu Kapatma',

                        'create_work_session' => 'Mesai Kaydı Ekleme',
                        'edit_work_session' => 'Mesai Kaydı Düzenleme',
                        'delete_work_session' => 'Mesai Kaydı Silme',
                    ]),

                SelectFilter::make('device')->label('Cihaz')->options([
                    'Desktop' => 'Desktop',
                    'Mobile' => 'Mobile',
                ]),

                SelectFilter::make('browser')->label('Tarayıcı')->options([
                    'Chrome' => 'Chrome',
                    'Opera' => 'Opera',
                    'Firefox' => 'Firefox',
                    'Edge' => 'Edge',
                    'Safari' => 'Safari',
                ]),

                SelectFilter::make('platform')->label('Platform')->options([
                    'Windows' => 'Windows',
                    'Linux' => 'Linux',
                    'Android' => 'Android',
                    'iOS' => 'iOS',
                    'MacOS' => 'MacOS',
                ]),
            ])

            ->recordActions([
                ViewAction::make()->label('Detay'),
                DeleteAction::make()->label('Sil')->color('danger'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Logları Sil'),
                ]),
            ]);
    }
}