<?php

namespace App\Filament\Resources\ForumTopics\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumTopicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Baslik'),
                TextEntry::make('category.name')->label('Kategori'),
                TextEntry::make('user.name')->label('Kullanici')->placeholder('Sistem'),
                TextEntry::make('status')->label('Durum')->badge(),
                TextEntry::make('ai_risk_label')->label('AI Risk')->badge(),
                TextEntry::make('ai_risk_score')->label('Risk Puan')->numeric(),
                TextEntry::make('ai_risk_reasons')->label('Risk Sebepleri')->listWithLineBreaks()->placeholder('-')->columnSpanFull(),
                IconEntry::make('is_pinned')->label('Sabit')->boolean(),
                IconEntry::make('is_locked')->label('Kilitli')->boolean(),
                IconEntry::make('is_solved')->label('Cozuldu')->boolean(),
                IconEntry::make('replies_closed')->label('Cevaplar Kapali')->boolean(),
                TextEntry::make('slow_mode_seconds')->label('Yavas Mod')->suffix(' sn')->numeric(),
                TextEntry::make('lastPostUser.name')->label('Son Cevaplayan')->placeholder('-'),
                TextEntry::make('views')->label('Goruntulenme')->numeric(),
                TextEntry::make('moderator_note')->label('Moderator Notu')->placeholder('-')->columnSpanFull(),
                TextEntry::make('content')->label('Icerik')->html()->columnSpanFull(),
                TextEntry::make('created_at')->label('Olusturma')->dateTime()->placeholder('-'),
            ]);
    }
}
