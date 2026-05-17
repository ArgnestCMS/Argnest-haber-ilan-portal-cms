<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('İşlem Bilgisi')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Kullanıcı')
                            ->placeholder('Sistem'),

                        TextEntry::make('action')
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
    'comment_rejected' => 'heroicon-o-x-circle',
    'comment_submitted' => 'heroicon-o-chat-bubble-left-right',

    'blocked_comment_attempt' => 'heroicon-o-no-symbol',

    'flood_comment_detected' => 'heroicon-o-exclamation-triangle',

    'spam_comment_rejected' => 'heroicon-o-shield-exclamation',

    'auto_punishment_applied' => 'heroicon-o-shield-exclamation',
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
    'create_work_session' => 'Mesai Kaydı Eklendi',
    'edit_work_session' => 'Mesai Kaydı Düzenlendi',
    'delete_work_session' => 'Mesai Kaydı Silindi',

    'comment_submitted' => 'Yorum Gönderildi',
    'blocked_comment_attempt' => 'Engelli Yorum Denemesi',
    'flood_comment_detected' => 'Flood Tespit Edildi',
    'spam_comment_rejected' => 'Spam Yorum Reddedildi',
    'auto_punishment_applied' => 'Otomatik Ceza Uygulandı',

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
 }),

                        TextEntry::make('description')
                            ->label('Açıklama')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('created_at')
                            ->label('Oluşturulma Tarihi')
                            ->dateTime('d.m.Y H:i:s')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Güncellenme Tarihi')
                            ->dateTime('d.m.Y H:i:s')
                            ->placeholder('-'),
                    ]),

                Section::make('Cihaz & Güvenlik Bilgisi')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('ip_address')
                            ->label('IP Adresi')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('device')
                            ->label('Cihaz')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Mobile' => 'success',
                                'Desktop' => 'info',
                                default => 'gray',
                            })
                            ->placeholder('-'),

                        TextEntry::make('browser')
                            ->label('Tarayıcı')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Chrome' => 'success',
                                'Opera' => 'danger',
                                'Firefox' => 'warning',
                                'Edge' => 'info',
                                'Safari' => 'gray',
                                default => 'gray',
                            })
                            ->placeholder('-'),

                        TextEntry::make('platform')
                            ->label('Platform')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'Windows' => 'info',
                                'Android' => 'success',
                                'iOS' => 'gray',
                                'MacOS' => 'warning',
                                'Linux' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('-'),

                        TextEntry::make('url')
                            ->label('URL')
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpan(2),

                        TextEntry::make('user_agent')
                            ->label('User Agent')
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ek Veriler')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('properties.email')
                            ->label('Email')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('properties.role')
                            ->label('Rol')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.title')
                            ->label('Başlık')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('properties.slug')
                            ->label('Slug')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('properties.old_ip')
                            ->label('Eski IP')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('properties.new_ip')
                            ->label('Yeni IP')
                            ->copyable()
                            ->placeholder('-'),

                        TextEntry::make('properties.old_device')
                            ->label('Eski Cihaz')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.new_device')
                            ->label('Yeni Cihaz')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.old_browser')
                            ->label('Eski Tarayıcı')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.new_browser')
                            ->label('Yeni Tarayıcı')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.old_platform')
                            ->label('Eski Platform')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.new_platform')
                            ->label('Yeni Platform')
                            ->badge()
                            ->placeholder('-'),

                        TextEntry::make('properties.seconds')
                            ->label('Kilit Süresi (sn)')
                            ->placeholder('-'),

                        TextEntry::make('properties')
                            ->label('Ham Veri')
                            ->formatStateUsing(function ($state): string {
                                if (blank($state)) {
                                    return '-';
                                }

                                if (is_array($state)) {
                                    return json_encode(
                                        $state,
                                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                    );
                                }

                                return (string) $state;
                            })
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}