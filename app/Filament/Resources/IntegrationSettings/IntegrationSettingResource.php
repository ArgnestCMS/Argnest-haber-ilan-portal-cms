<?php

namespace App\Filament\Resources\IntegrationSettings;

use App\Filament\Resources\IntegrationSettings\Pages\EditIntegrationSetting;
use App\Filament\Resources\IntegrationSettings\Pages\ListIntegrationSettings;
use App\Models\IntegrationSetting;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class IntegrationSettingResource extends Resource
{
    protected static ?string $model = IntegrationSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?string $navigationLabel = 'Sistem Ayarları';

    protected static ?string $modelLabel = 'Sistem Ayarı';

    protected static ?string $pluralModelLabel = 'Sistem Ayarları';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Önemli')
                    ->description('.env dosyası bu panelden düzenlenmez. DB değeri varsa runtime config için kullanılır, yoksa .env/config fallback çalışır. Config cache kullanıyorsanız değişikliklerden sonra optimize:clear gerekebilir.')
                    ->schema([]),

                Tabs::make('Sistem Ayarları')
                    ->tabs([
                        Tabs\Tab::make('Genel Site Ayarları')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Site adı')
                                    ->maxLength(255),

                                Textarea::make('site_description')
                                    ->label('Site açıklaması')
                                    ->rows(3),

                                TextInput::make('site_email')
                                    ->label('Site e-posta adresi')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('app_url_info')
                                    ->label('APP_URL')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(config('app.url')),

                                TextInput::make('timezone_info')
                                    ->label('Saat dilimi')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->default(config('app.timezone', 'UTC')),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Mail Ayarları')
                            ->schema([
                                Select::make('mail_mailer')
                                    ->label('MAIL_MAILER')
                                    ->options([
                                        'smtp' => 'smtp',
                                        'log' => 'log',
                                        'array' => 'array',
                                        'failover' => 'failover',
                                    ])
                                    ->placeholder(config('mail.default')),

                                TextInput::make('mail_host')
                                    ->label('MAIL_HOST')
                                    ->placeholder(config('mail.mailers.smtp.host'))
                                    ->maxLength(255),

                                TextInput::make('mail_port')
                                    ->label('MAIL_PORT')
                                    ->numeric()
                                    ->placeholder((string) config('mail.mailers.smtp.port')),

                                TextInput::make('mail_username')
                                    ->label('MAIL_USERNAME')
                                    ->placeholder(config('mail.mailers.smtp.username'))
                                    ->maxLength(255),

                                TextInput::make('mail_password')
                                    ->label('MAIL_PASSWORD')
                                    ->password()
                                    ->revealable()
                                    ->helperText('Gmail için normal hesap şifresi değil, uygulama şifresi kullanılmalıdır.')
                                    ->maxLength(1000),

                                Select::make('mail_encryption')
                                    ->label('MAIL_ENCRYPTION')
                                    ->options([
                                        'tls' => 'tls',
                                        'ssl' => 'ssl',
                                    ])
                                    ->placeholder((string) config('mail.mailers.smtp.scheme')),

                                TextInput::make('mail_from_address')
                                    ->label('MAIL_FROM_ADDRESS')
                                    ->email()
                                    ->placeholder(config('mail.from.address'))
                                    ->maxLength(255),

                                TextInput::make('mail_from_name')
                                    ->label('MAIL_FROM_NAME')
                                    ->placeholder(config('mail.from.name'))
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Google / reCAPTCHA')
                            ->schema([
                                Toggle::make('recaptcha_enabled')
                                    ->label('reCAPTCHA aktif')
                                    ->default(true),

                                TextInput::make('recaptcha_site_key')
                                    ->label('RECAPTCHA_SITE_KEY')
                                    ->placeholder(config('services.recaptcha.site_key'))
                                    ->maxLength(255),

                                TextInput::make('recaptcha_secret_key')
                                    ->label('RECAPTCHA_SECRET_KEY')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(1000),

                                Section::make('Not')
                                    ->description('Aktif edildiğinde register formunda Google doğrulaması gösterilir ve backend siteverify kontrolü yapılır. Anahtarlar boşsa .env fallback kullanılır.')
                                    ->schema([]),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Analytics / Tag Manager')
                            ->schema([
                                Textarea::make('google_analytics')
                                    ->label('Google Analytics ID veya script')
                                    ->rows(5),

                                Textarea::make('google_tag_manager')
                                    ->label('Google Tag Manager ID veya script')
                                    ->rows(5),
                            ]),

                        Tabs\Tab::make('PWA / Push')
                            ->schema([
                                Toggle::make('webpush_enabled')
                                    ->label('Push aktif')
                                    ->default(false),

                                Textarea::make('webpush_vapid_public_key')
                                    ->label('VAPID_PUBLIC_KEY')
                                    ->rows(3),

                                TextInput::make('webpush_vapid_private_key')
                                    ->label('VAPID_PRIVATE_KEY')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(2000),

                                TextInput::make('webpush_vapid_subject')
                                    ->label('VAPID_SUBJECT')
                                    ->placeholder(config('services.webpush.vapid.subject'))
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Sosyal Giriş / OAuth')
                            ->schema([
                                TextInput::make('google_client_id')
                                    ->label('Google Client ID')
                                    ->maxLength(255)
                                    ->helperText('Hazırlık alanı. Sosyal giriş akışı ayrıca bağlanmalıdır.'),

                                TextInput::make('google_client_secret')
                                    ->label('Google Client Secret')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(1000),

                                TextInput::make('facebook_app_id')
                                    ->label('Facebook App ID')
                                    ->maxLength(255)
                                    ->helperText('Hazırlık alanı. Sosyal giriş akışı ayrıca bağlanmalıdır.'),

                                TextInput::make('facebook_app_secret')
                                    ->label('Facebook App Secret')
                                    ->password()
                                    ->revealable()
                                    ->maxLength(1000),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Güvenlik')
                            ->schema([
                                Toggle::make('registration_enabled')
                                    ->label('Kayıt sistemi aktif'),

                                Toggle::make('email_verification_required')
                                    ->label('Mail doğrulama zorunlu'),

                                Toggle::make('captcha_required')
                                    ->label('Captcha zorunlu')
                                    ->default(true),

                                Section::make('Login rate limit')
                                    ->description('Login POST route throttle:10,1 ile korunur; register POST route throttle:5,1 ile korunur.')
                                    ->schema([]),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#'),
                TextColumn::make('mail_mailer')
                    ->label('Mailer')
                    ->placeholder(config('mail.default')),
                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->recordActions([
                EditAction::make()->label('Düzenle'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrationSettings::route('/'),
            'edit' => EditIntegrationSetting::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::isAllowed();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return static::isAllowed();
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAllowed();
    }

    private static function isAllowed(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin()
            || $user?->role === 'super_admin'
            || $user?->roleModel?->slug === 'super_admin';
    }
}
