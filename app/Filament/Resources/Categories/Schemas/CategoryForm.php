<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
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
                            Category::where('slug', $slug)
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
                    ->label('URL (Slug)')
                    ->required()
                    ->unique(
                        table: Category::class,
                        column: 'slug',
                        ignoreRecord: true
                    ),

                Select::make('type')
                    ->label('Kategori Türü')
                    ->options([
                        'news' => 'Haber',
                        'announcement' => 'İlan',
                    ])
                    ->default('news')
                    ->required(),

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