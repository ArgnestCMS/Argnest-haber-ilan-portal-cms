<?php

namespace App\Filament\Resources\ForumCategories\Schemas;

use App\Models\ForumCategory;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ForumCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Kategori Adı')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        $baseSlug = Str::slug($state);
                        $slug = $baseSlug;
                        $counter = 2;

                        while (
                            ForumCategory::withTrashed()
                                ->where('slug', $slug)
                                ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                ->exists()
                        ) {
                            $slug = $baseSlug . '-' . $counter;
                            $counter++;
                        }

                        $set('slug', $slug);
                    })
                    ->required(),

                TextInput::make('slug')
                    ->label('URL')
                    ->required()
                    ->unique(table: ForumCategory::class, column: 'slug', ignoreRecord: true),

                Textarea::make('description')
                    ->label('Açıklama')
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
