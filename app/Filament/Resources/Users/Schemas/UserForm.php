<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Sekmeler')
                    ->tabs([

                        Tabs\Tab::make('Genel Bilgiler')
                            ->schema([

                                TextInput::make('name')
                                    ->label('Ad Soyad')
                                    ->required(),

                                TextInput::make('email')
                                    ->label('E-Posta')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Bu e-posta adresi zaten kullanılıyor. Lütfen farklı bir e-posta adresi girin.',
                                    ])
                                    ->required(),

                                Select::make('role_id')
                                    ->label('Rol')
                                    ->relationship('roleModel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(
                                        fn () => Role::where('slug', 'user')->value('id')
                                    ),

                                Select::make('status')
                                    ->label('Hesap Durumu')
                                    ->options([
                                        'active' => 'Aktif',
                                        'suspended' => 'Askıya Alındı',
                                        'banned' => 'Banlandı',
                                        'frozen' => 'Donduruldu',
                                    ])
                                    ->required()
                                    ->default('active'),

                                Toggle::make('is_active')
                                    ->label('Hesap Aktif')
                                    ->default(true),

                                DateTimePicker::make('email_verified_at')
                                    ->label('E-Posta Doğrulama Tarihi'),

                                DateTimePicker::make('suspended_until')
                                    ->label('Askı Bitiş Tarihi'),

                                Textarea::make('ban_reason')
                                    ->label('Ban / Askı Sebebi')
                                    ->rows(4)
                                    ->columnSpanFull(),

                                TextInput::make('password')
                                    ->label('Şifre')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state)
                                        ? Hash::make($state)
                                        : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create'),

                            ]),

                        Tabs\Tab::make('Profil')
                            ->schema([

                                FileUpload::make('avatar')
                                    ->label('Profil Resmi')
                                    ->image()
                                    ->directory('avatars'),

                                Textarea::make('bio')
                                    ->label('Biyografi')
                                    ->rows(6)
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('Sosyal Medya')
                            ->schema([

                                TextInput::make('facebook')
                                    ->label('Facebook'),

                                TextInput::make('twitter')
                                    ->label('Twitter'),

                                TextInput::make('instagram')
                                    ->label('Instagram'),

                                TextInput::make('youtube')
                                    ->label('YouTube'),

                            ]),

                    ]),

            ]);
    }
}