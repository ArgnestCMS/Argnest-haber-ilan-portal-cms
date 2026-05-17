<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PendingCommentsWidget extends TableWidget
{
    protected static ?string $heading = 'Bekleyen Yorumlar';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Comment::query()
                    ->with('user')
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(10)
            )
            ->defaultPaginationPageOption(10)
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->placeholder('Misafir'),

                TextColumn::make('content')
                    ->label('Yorum')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('commentable_type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'App\Models\News' => 'Haber',
                        'App\Models\Announcement' => 'İlan',
                        default => 'İçerik',
                    }),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('-')
                    ->copyable(),
            ]);
    }

    public static function canView(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('yorum_moderasyonu')
        || auth()->user()?->hasPermission('forum_moderasyonu');
}
}