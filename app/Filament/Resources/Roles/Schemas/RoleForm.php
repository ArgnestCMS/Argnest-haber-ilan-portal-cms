<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Rol Bilgileri')
                    ->schema([

                        TextInput::make('name')
                            ->label('Rol Adı')
                            ->required()
                            ->disabled(fn ($record): bool => (bool) $record?->is_system)
                            ->dehydrated()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->disabled(fn ($record): bool => (bool) $record?->is_system)
                            ->dehydrated()
                            ->unique(ignoreRecord: true),

                        TextInput::make('color')
                            ->label('Renk')
                            ->default('primary')
                            ->required(),

                        Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(4)
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                Section::make('Yetkiler')
                    ->description('Bu role atanacak panel ve sistem yetkilerini seçin.')
                    ->schema([

                        CheckboxList::make('permissions')
                            ->label('Yetkiler')
                            ->relationship('permissions', 'name')
                            ->columns(2)
                            ->searchable()
                            ->bulkToggleable(),

                    ]),

            ]);
    }
}