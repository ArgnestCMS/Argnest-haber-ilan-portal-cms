<?php

namespace App\Filament\Resources\HeaderSlots\Schemas;

use App\Models\HeaderSlot;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class HeaderSlotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Header Slot Yönetimi')
                    ->tabs([
                        Tabs\Tab::make('Genel')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Başlık / ad')
                                    ->required()
                                    ->maxLength(255)
                                    ->dehydratedWhenHidden(),

                                Select::make('slot_type')
                                    ->label('Slot Tipi')
                                    ->options([
                                        HeaderSlot::TYPE_BUTTON => 'Buton',
                                        HeaderSlot::TYPE_BANNER => 'Reklam / Banner',
                                    ])
                                    ->default(HeaderSlot::TYPE_BUTTON)
                                    ->live()
                                    ->required()
                                    ->dehydratedWhenHidden(),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->dehydratedWhenHidden(),

                                TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->required()
                                    ->dehydratedWhenHidden(),

                                Select::make('display_position')
                                    ->label('Gösterim konumu')
                                    ->options([
                                        HeaderSlot::POSITION_TOPBAR_AFTER_HOME => 'Ana Sayfa yanındaki üst bar alanı',
                                    ])
                                    ->default(HeaderSlot::POSITION_TOPBAR_AFTER_HOME)
                                    ->required()
                                    ->dehydratedWhenHidden(),

                                DateTimePicker::make('starts_at')
                                    ->label('Başlangıç tarihi')
                                    ->dehydratedWhenHidden(),

                                DateTimePicker::make('ends_at')
                                    ->label('Bitiş tarihi')
                                    ->dehydratedWhenHidden(),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Buton Modu')
                            ->schema([
                                Section::make('Buton Ayarları')
                                    ->schema([
                                        TextInput::make('button_text')
                                            ->label('Buton yazısı')
                                            ->maxLength(120)
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('button_url')
                                            ->label('Link URL')
                                            ->url()
                                            ->dehydratedWhenHidden(),

                                        Select::make('button_target')
                                            ->label('Link hedefi')
                                            ->options([
                                                '_self' => 'Aynı sekme',
                                                '_blank' => 'Yeni sekme',
                                            ])
                                            ->default('_self')
                                            ->dehydratedWhenHidden(),

                                        ColorPicker::make('button_background_color')
                                            ->label('Arka plan rengi')
                                            ->dehydratedWhenHidden(),

                                        ColorPicker::make('button_hover_color')
                                            ->label('Hover rengi')
                                            ->dehydratedWhenHidden(),

                                        ColorPicker::make('button_text_color')
                                            ->label('Yazı rengi')
                                            ->dehydratedWhenHidden(),

                                        Select::make('button_size')
                                            ->label('Buton boyutu')
                                            ->options([
                                                'small' => 'Küçük',
                                                'medium' => 'Orta',
                                                'large' => 'Büyük',
                                            ])
                                            ->default('medium')
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('button_radius')
                                            ->label('Border radius')
                                            ->numeric()
                                            ->suffix('px')
                                            ->default(6)
                                            ->minValue(0)
                                            ->maxValue(48)
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('button_icon')
                                            ->label('Opsiyonel ikon/emoji')
                                            ->maxLength(24)
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('custom_css_class')
                                            ->label('Opsiyonel özel CSS class')
                                            ->helperText('Sadece güvenilir ve mevcut CSS sınıfları kullanılmalıdır.')
                                            ->maxLength(255)
                                            ->dehydratedWhenHidden(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Reklam / Banner Modu')
                            ->schema([
                                Section::make('Görsel Banner')
                                    ->schema([
                                        FileUpload::make('banner_image')
                                            ->label('Banner görseli')
                                            ->image()
                                            ->disk('public')
                                            ->directory('header-slots')
                                            ->visibility('public')
                                            ->downloadable()
                                            ->openable()
                                            ->nullable()
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('banner_url')
                                            ->label('Banner linki')
                                            ->url()
                                            ->dehydratedWhenHidden(),

                                        Select::make('banner_target')
                                            ->label('Link hedefi')
                                            ->options([
                                                '_self' => 'Aynı sekme',
                                                '_blank' => 'Yeni sekme',
                                            ])
                                            ->default('_self')
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('banner_width')
                                            ->label('Banner genişliği')
                                            ->numeric()
                                            ->suffix('px')
                                            ->minValue(1)
                                            ->maxValue(1200)
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('banner_height')
                                            ->label('Banner yüksekliği')
                                            ->numeric()
                                            ->suffix('px')
                                            ->minValue(1)
                                            ->maxValue(400)
                                            ->dehydratedWhenHidden(),

                                        TextInput::make('banner_alt')
                                            ->label('Alt text')
                                            ->maxLength(255)
                                            ->dehydratedWhenHidden(),
                                    ])
                                    ->columns(2),

                                Section::make('Kod Alanları')
                                    ->description('HTML ve script alanları risklidir. Yalnızca güvenilir reklam kodlarını kullanın.')
                                    ->schema([
                                        Placeholder::make('code_warning')
                                            ->label('Güvenlik uyarısı')
                                            ->content(new HtmlString('<strong>Bu alanlar public frontend içinde render edilir.</strong> Script kodu yalnızca admin erişimli bu panelden girilmelidir.')),

                                        Textarea::make('html_code')
                                            ->label('Opsiyonel HTML kod alanı')
                                            ->rows(6)
                                            ->columnSpanFull()
                                            ->dehydratedWhenHidden(),

                                        Textarea::make('script_code')
                                            ->label('Opsiyonel script kod alanı')
                                            ->helperText('Sadece admin/super admin kullanımı içindir. Boşsa frontendde render edilmez.')
                                            ->rows(6)
                                            ->columnSpanFull()
                                            ->dehydratedWhenHidden(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
