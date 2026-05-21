<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('commentable_type')
                    ->label('İçerik Türü')
                    ->required(),
                TextInput::make('commentable_id')
                    ->label('İçerik ID')
                    ->required()
                    ->numeric(),
                Textarea::make('content')
                    ->label('Yorum')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Durum')
                    ->options(['pending' => 'Bekliyor', 'approved' => 'Onaylandı', 'rejected' => 'Reddedildi'])
                    ->default('pending')
                    ->required(),
                TextInput::make('moderated_by')
                    ->label('Moderatör ID')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('moderated_at')
                    ->label('Moderasyon Tarihi'),
                Textarea::make('moderation_note')
                    ->label('Moderasyon Notu')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ip_address')
                    ->label('IP Adresi')
                    ->default(null),
                Toggle::make('is_edited')
                    ->label('Düzenlendi')
                    ->required(),
            ]);
    }
}
