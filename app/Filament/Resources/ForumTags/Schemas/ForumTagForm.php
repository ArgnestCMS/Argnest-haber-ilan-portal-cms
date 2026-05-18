<?php

namespace App\Filament\Resources\ForumTags\Schemas;

use App\Models\ForumTag;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ForumTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Etiket Adi')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        $baseSlug = Str::slug($state);
                        $slug = $baseSlug;
                        $counter = 2;

                        while (
                            ForumTag::query()
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
                    ->unique(table: ForumTag::class, column: 'slug', ignoreRecord: true),

                TextInput::make('color')
                    ->label('Renk')
                    ->default('#ef4444')
                    ->maxLength(20),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Siralama')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
