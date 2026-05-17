<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')

            ->columns([

                TextColumn::make('name')
                    ->label('Rol Adı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->searchable(),

                TextColumn::make('color')
                    ->label('Renk')
                    ->badge(),

                TextColumn::make('users_count')
                    ->label('Kullanıcı')
                    ->counts('users')
                    ->badge()
                    ->color('info'),

                TextColumn::make('permissions_count')
                    ->label('Yetki')
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_system')
                    ->label('Sistem Rolü')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([
                //
            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                Action::make('make_system')
                    ->label('Sistem Rolü Yap')
                    ->icon('heroicon-o-shield-check')
                    ->color('danger')
                    ->visible(fn ($record): bool => ! $record->is_system)
                    ->requiresConfirmation()
                    ->modalHeading('Bu rol sistem rolü yapılsın mı?')
                    ->modalDescription('Bu işlemden sonra bu rol silinemez. Rol adı ve slug alanları kilitlenir. Sadece yetkileri düzenlenebilir. Devam etmek istiyor musunuz?')
                    ->modalSubmitActionLabel('Kabul Ediyorum')
                    ->modalCancelActionLabel('Vazgeç')
                    ->action(function ($record): void {
                        $record->update([
                            'is_system' => true,
                        ]);
                    }),

                DeleteAction::make()
                    ->visible(fn ($record): bool => ! $record->is_system),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}