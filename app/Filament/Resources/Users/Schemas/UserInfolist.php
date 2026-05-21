<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Ad Soyad'),
                TextEntry::make('email')
                    ->label('E-Posta'),
                TextEntry::make('role')
                    ->label('Rol'),
                TextEntry::make('email_verified_at')
                    ->label('E-Posta Doğrulama Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->label('Profil Resmi')
                    ->placeholder('-'),
                TextEntry::make('bio')
                    ->label('Biyografi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('facebook')
                    ->placeholder('-'),
                TextEntry::make('twitter')
                    ->placeholder('-'),
                TextEntry::make('instagram')
                    ->placeholder('-'),
                TextEntry::make('youtube')
                    ->placeholder('-'),
            ]);
    }
}
