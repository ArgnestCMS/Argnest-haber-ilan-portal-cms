<?php

namespace App\Filament\Resources\SiteSettings\Schemas;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Site Ayarları')
                    ->tabs([

                        /*
                        |--------------------------------------------------------------------------
                        | GENEL
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('Genel Ayarlar')
                            ->schema([

                                TextInput::make('site_name')
                                    ->label('Site Adı')
                                    ->required(),

                                TextInput::make('site_slogan')
                                    ->label('Site Sloganı'),

                                FileUpload::make('logo')
                                    ->label('Logo')
                                    ->image()
                                    ->directory('site'),

                                FileUpload::make('favicon')
                                    ->label('Favicon')
                                    ->image()
                                    ->directory('site'),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | SEO
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('SEO')
                            ->schema([

                                TextInput::make('seo_title')
                                    ->label('SEO Başlığı'),

                                Textarea::make('seo_description')
                                    ->label('SEO Açıklaması')
                                    ->rows(5)
                                    ->columnSpanFull(),

                                Textarea::make('seo_keywords')
                                    ->label('SEO Anahtar Kelimeler')
                                    ->rows(4)
                                    ->columnSpanFull(),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | İLETİŞİM
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('İletişim')
                            ->schema([

                                TextInput::make('email')
                                    ->label('E-Posta')
                                    ->email(),

                                TextInput::make('phone')
                                    ->label('Telefon'),

                                TextInput::make('address')
                                    ->label('Adres'),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | SOSYAL MEDYA
                        |--------------------------------------------------------------------------
                        */

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

                                TextInput::make('telegram')
                                    ->label('Telegram'),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | KOD ALANLARI
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('Kod Alanları')
                            ->schema([

                                Textarea::make('header_scripts')
                                    ->label('Header Kodları')
                                    ->rows(8)
                                    ->columnSpanFull(),

                                Textarea::make('footer_scripts')
                                    ->label('Footer Kodları')
                                    ->rows(8)
                                    ->columnSpanFull(),

                                Textarea::make('google_analytics')
                                    ->label('Google Analytics')
                                    ->rows(6)
                                    ->columnSpanFull(),

                                Textarea::make('adsense_code')
                                    ->label('Adsense Kodu')
                                    ->rows(6)
                                    ->columnSpanFull(),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | FOOTER
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('Footer')
                            ->schema([

                                Textarea::make('footer_about')
                                    ->label('Footer Hakkında Yazısı')
                                    ->rows(6)
                                    ->columnSpanFull(),

                                Textarea::make('footer_copyright')
                                    ->label('Copyright Yazısı')
                                    ->rows(3)
                                    ->columnSpanFull(),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | SİSTEM
                        |--------------------------------------------------------------------------
                        */

                        Tabs\Tab::make('Sistem')
                        ->schema([

        Toggle::make('maintenance_mode')
            ->label('Bakım Modu'),

        Textarea::make('maintenance_message')
            ->label('Bakım Mesajı')
            ->rows(3)
            ->columnSpanFull(),

        DateTimePicker::make('maintenance_ends_at')
            ->label('Tahmini Bitiş Tarihi')
            ->seconds(false),

        Toggle::make('auto_punishment_enabled')
            ->label('Otomatik Ceza Sistemi')
            ->helperText('Spam, flood ve kötüye kullanım tespitlerinde otomatik mute/ban sistemi uygulanır.'),

    ]),
    Tabs\Tab::make('Ana Sayfa Modül Ayarları')
    ->schema([

        Toggle::make('home_news_enabled')
            ->label('Haberler')
            ->default(true)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_announcements_enabled')
            ->label('İlanlar')
            ->default(true)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_forum_enabled')
            ->label('Forum')
            ->default(false)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_galleries_enabled')
            ->label('Galeriler')
            ->default(true)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_videos_enabled')
            ->label('Videolar')
            ->default(true)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_polls_enabled')
            ->label('Anketler')
            ->default(false)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_breaking_news_enabled')
            ->label('Son Dakika')
            ->default(false)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

        Toggle::make('home_announcement_bar_enabled')
            ->label('Duyuru barı')
            ->default(false)
            ->helperText('Bu ayar ana sayfada hangi içerik bloklarının görüneceğini belirler.'),

    ])
    ->columns(2),
    Tabs\Tab::make('Hava Durumu')
    ->schema([

        Toggle::make('weather_enabled')
            ->label('Hava durumu aktif')
            ->default(true)
            ->helperText('Kapaliysa frontend hava durumu alani gosterilmez.'),

        TextInput::make('weather_local_fallback_city')
            ->label('Local fallback sehri')
            ->placeholder('Istanbul')
            ->maxLength(255)
            ->helperText('Localhost/127.0.0.1 gelistirme ortaminda kullanilir.'),

        TextInput::make('weather_cache_minutes')
            ->label('Cache suresi')
            ->numeric()
            ->minValue(10)
            ->maxValue(15)
            ->default(10)
            ->suffix('dk')
            ->helperText('IP konum ve hava durumu cache suresi.'),

    ])
    ->columns(3),
    Tabs\Tab::make('Topluluk & Canlı Sistem')
    ->schema([

        Toggle::make('forum_enabled')
            ->label('Forum Aktif')
            ->helperText('Kapatılırsa kullanıcılar forum alanına erişemez.'),

        Toggle::make('live_chat_enabled')
            ->label('Canlı Sohbet Aktif')
            ->helperText('Kapatılırsa canlı sohbet sayfası kullanıcıya kapalı görünür.'),

        Toggle::make('live_stream_enabled')
            ->label('Canlı Yayın Aktif')
            ->helperText('Canlı sohbet sayfasında yayın alanını gösterir.'),

        TextInput::make('live_stream_title')
            ->label('Canlı Yayın Başlığı')
            ->maxLength(255),

        Textarea::make('live_stream_description')
            ->label('Canlı Yayın Açıklaması')
            ->rows(3)
            ->columnSpanFull(),

        TextInput::make('live_stream_url')
            ->label('Canlı Yayın URL')
            ->url()
            ->helperText('YouTube canlı yayın veya embed linki girilebilir.'),

        Toggle::make('live_announcement_enabled')
            ->label('Canlı Duyuru Aktif'),

        TextInput::make('live_announcement_text')
            ->label('Canlı Duyuru Metni')
            ->maxLength(255)
            ->columnSpanFull(),

        Select::make('live_announcement_type')
            ->label('Duyuru Türü')
            ->options([
                'info' => 'Bilgi',
                'warning' => 'Uyarı',
                'danger' => 'Son Dakika',
                'success' => 'Başarılı',
            ])
            ->default('info'),

    ])
    ->columns(2),
    /*
|--------------------------------------------------------------------------
| ÜYELİK
|--------------------------------------------------------------------------
*/

Tabs\Tab::make('Üyelik Ayarları')
    ->schema([

        Toggle::make('registration_enabled')
            ->label('Yeni Üyelikler Açık')
            ->helperText('Kapatılırsa yeni kullanıcı kayıtları engellenir.'),

        Toggle::make('email_verification_required')
            ->label('E-Posta Doğrulama Zorunlu')
            ->helperText('Kullanıcılar mail doğrulamadan sistemi tam kullanamaz.'),

        RichEditor::make('membership_agreement')
            ->label('Üyelik Sözleşmesi')
            ->columnSpanFull(),

        RichEditor::make('privacy_policy')
            ->label('Gizlilik Politikası')
            ->columnSpanFull(),

        RichEditor::make('community_rules')
            ->label('Topluluk Kuralları')
            ->columnSpanFull(),

    ]),

                    ])

            ]);
    }
}
