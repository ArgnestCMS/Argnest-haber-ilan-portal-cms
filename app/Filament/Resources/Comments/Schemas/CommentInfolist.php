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
                    ->label('User'),
                TextEntry::make('commentable_type'),
                TextEntry::make('commentable_id')
                    ->numeric(),
                TextEntry::make('content')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('moderated_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('moderated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('moderation_note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('ip_address')
                    ->placeholder('-'),
                IconEntry::make('is_edited')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
