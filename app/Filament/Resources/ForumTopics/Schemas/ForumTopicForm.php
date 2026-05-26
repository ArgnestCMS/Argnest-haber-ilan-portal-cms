<?php

namespace App\Filament\Resources\ForumTopics\Schemas;

use App\Models\ForumTopic;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ForumTopicForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('forum_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),

                TextInput::make('title')
                    ->label('Başlık')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        $baseSlug = Str::slug($state);
                        $slug = $baseSlug;
                        $counter = 2;

                        while (
                            ForumTopic::withTrashed()
                                ->where('slug', $slug)
                                ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                ->exists()
                        ) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        }

                        $set('slug', $slug);
                    })
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('URL')
                    ->required()
                    ->unique(table: ForumTopic::class, column: 'slug', ignoreRecord: true)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('İçerik')
                    ->required()
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'published' => 'Yayında',
                        'hidden' => 'Gizli',
                    ])
                    ->default('published')
                    ->required(),

                Select::make('tags')
                    ->label('Etiketler')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),

                Toggle::make('is_pinned')
                    ->label('Sabit')
                    ->default(false),

                Toggle::make('is_locked')
                    ->label('Kilitli')
                    ->default(false),

                Toggle::make('is_solved')
                    ->label('Çözüldü')
                    ->default(false),

                Toggle::make('replies_closed')
                    ->label('Cevaplar Kapali')
                    ->default(false),

                TextInput::make('slow_mode_seconds')
                    ->label('Yavas Mod (saniye)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(3600)
                    ->default(0),

                Textarea::make('moderator_note')
                    ->label('Moderatör Notu')
                    ->maxLength(5000)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
