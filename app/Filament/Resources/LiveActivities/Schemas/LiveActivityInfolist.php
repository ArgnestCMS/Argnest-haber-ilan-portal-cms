<?php

namespace App\Filament\Resources\LiveActivities\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LiveActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Aktivite Bilgisi')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('title')
                            ->label('Başlık')
                            ->columnSpanFull(),

                        TextEntry::make('message')
                            ->label('Mesaj')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('user.name')
                            ->label('Kullanıcı')
                            ->placeholder('Sistem'),

                        TextEntry::make('type')
                            ->label('Tip')
                            ->badge(),

                        TextEntry::make('source')
                            ->label('Kaynak')
                            ->badge(),

                        TextEntry::make('severity')
                            ->label('Seviye')
                            ->badge(),

                        IconEntry::make('is_public')
                            ->label('Akışta Görünür')
                            ->boolean(),

                        IconEntry::make('is_important')
                            ->label('Önemli')
                            ->boolean(),

                        TextEntry::make('url')
                            ->label('URL')
                            ->copyable()
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('occurred_at')
                            ->label('Aktivite Zamanı')
                            ->dateTime('d.m.Y H:i:s'),
                    ]),

                Section::make('Bağlantılı Kayıt')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('subject_type')
                            ->label('Kayıt Tipi')
                            ->placeholder('-'),

                        TextEntry::make('subject_id')
                            ->label('Kayıt ID')
                            ->placeholder('-'),
                    ]),

                Section::make('Ek Veriler')
                    ->schema([
                        TextEntry::make('metadata')
                            ->label('Metadata')
                            ->formatStateUsing(function ($state): string {
                                if (blank($state)) {
                                    return '-';
                                }

                                if (is_array($state)) {
                                    return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                }

                                return (string) $state;
                            })
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
