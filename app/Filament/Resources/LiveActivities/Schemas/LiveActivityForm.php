<?php

namespace App\Filament\Resources\LiveActivities\Schemas;

use App\Models\LiveActivity;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LiveActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Başlık')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->label('Mesaj')
                    ->rows(4)
                    ->columnSpanFull(),

                Select::make('type')
                    ->label('Tip')
                    ->options([
                        'user_login' => 'Kullanıcı Girişi',
                        'user_logout' => 'Kullanıcı Çıkışı',
                        'forum_topic_created' => 'Yeni Forum Konusu',
                        'forum_post_created' => 'Yeni Forum Cevabı',
                        'live_chat_message' => 'Canlı Sohbet Mesajı',
                    ])
                    ->required()
                    ->searchable(),

                Select::make('source')
                    ->label('Kaynak')
                    ->options(array_combine(LiveActivity::SOURCES, LiveActivity::SOURCES))
                    ->required(),

                Select::make('severity')
                    ->label('Seviye')
                    ->options([
                        'info' => 'Bilgi',
                        'success' => 'Aktif',
                        'warning' => 'Uyarı',
                        'danger' => 'Kritik',
                    ])
                    ->required(),

                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DateTimePicker::make('occurred_at')
                    ->label('Aktivite Zamanı')
                    ->required(),

                Toggle::make('is_public')
                    ->label('Akışta Görünsün'),

                Toggle::make('is_important')
                    ->label('Önemli Aktivite'),
            ])
            ->columns(2);
    }
}
