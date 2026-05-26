<?php

namespace App\Filament\Resources\LiveChatMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LiveChatMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Kullanıcı')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ])
                    ->required()
                    ->default('approved'),

                Textarea::make('message')
                    ->label('Mesaj')
                    ->rows(5)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('moderation_note')
                    ->label('Moderasyon Notu')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
