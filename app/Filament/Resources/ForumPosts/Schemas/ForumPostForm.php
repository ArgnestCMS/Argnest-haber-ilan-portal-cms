<?php

namespace App\Filament\Resources\ForumPosts\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ForumPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('forum_topic_id')
                    ->label('Konu')
                    ->relationship('topic', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),

                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    ])
                    ->default('approved')
                    ->required(),

                RichEditor::make('content')
                    ->label('Cevap')
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
