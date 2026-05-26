<?php

namespace App\Filament\Resources\Institutions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class InstitutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->label('Kurum Adı')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    })
                    ->required(),

                TextInput::make('slug')
                    ->label('URL (Slug)')
                    ->required(),

                FileUpload::make('logo')
                    ->label('Kurum Logosu')
                    ->image(),

                TextInput::make('website')
                    ->label('Web Sitesi')
                    ->url(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

            ]);
    }
}