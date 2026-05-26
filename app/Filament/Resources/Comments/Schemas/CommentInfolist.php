<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Kullanıcı'),
                TextEntry::make('commentable_type')
                    ->label('İçerik Türü'),
                TextEntry::make('commentable_id')
                    ->label('İçerik ID')
                    ->numeric(),
                TextEntry::make('content')
                    ->label('Yorum')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->label('Durum')
                    ->badge(),
                TextEntry::make('ai_risk_label')
                    ->label('AI Risk')
                    ->badge(),
                TextEntry::make('ai_risk_score')
                    ->label('Risk Puan')
                    ->numeric(),
                TextEntry::make('ai_risk_reasons')
                    ->label('Risk Sebepleri')
                    ->listWithLineBreaks()
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('moderated_by')
                    ->label('Moderatör ID')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('moderated_at')
                    ->label('Moderasyon Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('moderation_note')
                    ->label('Moderasyon Notu')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->label('IP Adresi')
                    ->placeholder('-'),
                IconEntry::make('is_edited')
                    ->label('Düzenlendi')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
