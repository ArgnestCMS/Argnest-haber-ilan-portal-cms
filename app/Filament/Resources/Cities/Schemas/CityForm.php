<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Şehir Adı')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    })
                    ->required(),

                TextInput::make('slug')
                    ->label('URL (Slug)')
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

            ]);
    }
}