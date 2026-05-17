<?php

namespace App\Filament\Resources\UserPunishments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserPunishmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('user_id')
                    ->label('Ceza Verilecek Kullanıcı')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('moderator_id')
                    ->label('İşlemi Yapan Moderatör')
                    ->relationship('moderator', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id()),

                Select::make('type')
                    ->label('Ceza Türü')
                    ->options([
                        'warning' => 'Uyarı',
                        'mute' => 'Yorum Susturma',
                        'temporary_ban' => 'Süreli Ban',
                        'permanent_ban' => 'Süresiz Ban',
                    ])
                    ->required(),

                Textarea::make('reason')
                    ->label('Ceza Sebebi')
                    ->placeholder('Küfür, argo, hakaret, spam veya topluluk kurallarına aykırı davranış sebebini yazın...')
                    ->rows(5)
                    ->required()
                    ->columnSpanFull(),

                DateTimePicker::make('expires_at')
                    ->label('Ceza Bitiş Tarihi')
                    ->helperText('Süresiz ban için boş bırakın.'),

                Toggle::make('is_active')
                    ->label('Ceza Aktif')
                    ->default(true)
                    ->required(),

            ]);
    }
}