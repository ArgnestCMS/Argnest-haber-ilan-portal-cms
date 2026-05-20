<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Category;
use App\Models\City;
use App\Models\Institution;
use App\Models\MediaAsset;
use App\Support\ContentAttachmentFilenames;
use App\Support\ContentAttachmentLimits;
use App\Support\ContentHtml;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('İlan editörü')
                    ->tabs([
                        Tabs\Tab::make('İçerik')
                            ->schema([
                                Section::make('Temel bilgiler')
                                    ->description('Başlık, URL, kurum, şehir ve kategori seçimlerini buradan yönetin.')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('İlan Başlığı')
                                            ->helperText('Liste, detay ve SEO başlığı olarak kullanılır. Kurum ve pozisyon bilgisini açık yazın.')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('slug', Str::slug($state));
                                            })
                                            ->required(),

                                        TextInput::make('slug')
                                            ->label('URL (Slug)')
                                            ->helperText('Başlıktan otomatik oluşur. Yayına çıktıktan sonra mümkünse değiştirmeyin.')
                                            ->maxLength(255)
                                            ->required(),

                                        Select::make('institution')
                                            ->label('Kurum')
                                            ->helperText('Aktif kurumlar içinde arama yaparak seçin; gerekirse elle değer korunur.')
                                            ->options(fn () => Institution::where('is_active', true)
                                                ->orderBy('name')
                                                ->pluck('name', 'name'))
                                            ->searchable()
                                            ->preload(),

                                        Select::make('city')
                                            ->label('Şehir')
                                            ->helperText('İlanın geçerli olduğu şehir. Ulusal ilanlarda boş bırakılabilir.')
                                            ->options(fn () => City::where('is_active', true)
                                                ->orderBy('name')
                                                ->pluck('name', 'name'))
                                            ->searchable()
                                            ->preload(),

                                        Select::make('category_id')
                                            ->label('Kategori')
                                            ->helperText('Yeni kategori sistemi için ana sınıflandırma.')
                                            ->options(fn () => Category::where('type', 'announcement')
                                                ->where('is_active', true)
                                                ->orderBy('sort_order')
                                                ->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload(),

                                        Select::make('category')
                                            ->label('Eski Kategori Adı')
                                            ->helperText('Eski veri uyumluluğu için korunur; yeni içerikte mümkünse üstteki kategori alanını kullanın.')
                                            ->options(fn () => Category::where('type', 'announcement')
                                                ->where('is_active', true)
                                                ->orderBy('name')
                                                ->pluck('name', 'name'))
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(2),

                                Section::make('Yayın bilgileri')
                                    ->description('Kaynak, yayın tarihi ve başvuru tarihlerini düzenleyin.')
                                    ->schema([
                                        TextInput::make('source')
                                            ->label('Kaynak')
                                            ->helperText('Resmi duyuru bağlantısı, kurum adı veya kaynak notu.')
                                            ->maxLength(255),

                                        DatePicker::make('publish_date')
                                            ->label('Yayın Tarihi')
                                            ->helperText('İlanın yayınlanma veya duyuru tarihi.'),

                                        DatePicker::make('deadline')
                                            ->label('Son Başvuru Tarihi')
                                            ->helperText('Başvuru süresi varsa son günü seçin.'),
                                    ])
                                    ->columns(3),

                                Section::make('Özet ve ilan metni')
                                    ->description('Kartlarda görünen kısa özet ve tam ilan içeriği.')
                                    ->schema([
                                        Textarea::make('summary')
                                            ->label('Kısa Açıklama')
                                            ->helperText('Liste kartları ve meta description için 140-160 karakter idealdir.')
                                            ->rows(5)
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        RichEditor::make('content')
                                            ->label('İlan İçeriği')
                                            ->helperText('Başvuru şartları, kontenjan ve tarihleri okunabilir başlıklarla ayırın.')
                                            ->required()
                                            ->extraInputAttributes([
                                                'style' => 'min-height: 500px;',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Medya')
                            ->schema([
                                Section::make('İlan görseli')
                                    ->description('Liste ve detay sayfasında kullanılacak ana görsel.')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('İlan Görseli')
                                            ->helperText('Yatay, net ve okunabilir JPG, PNG veya WEBP görsel tercih edin.')
                                            ->image()
                                            ->disk('public')
                                            ->directory('announcements')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120)
                                            ->previewable(false)
                                            ->downloadable()
                                            ->openable(),
                                    ]),

                                Section::make('Ek doküman')
                                    ->description('Resmi PDF veya başvuru dokümanı.')
                                    ->schema([
                                        FileUpload::make('document')
                                            ->label('PDF / Doküman')
                                            ->helperText('Resmi duyuru PDF’i veya başvuru dosyası varsa ekleyin.')
                                            ->disk('public')
                                            ->directory('announcements/documents')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(fn () => ContentAttachmentLimits::maxKilobytes())
                                            ->previewable(false)
                                            ->downloadable()
                                            ->openable(),
                                    ]),

                                Section::make('Toplu içerik dosyaları')
                                    ->description('İlana bağlı birden fazla görsel veya dokümanı yükleyin; kayıttan sonra listeden kopyalayabilir ya da içeriğe ekleyebilirsiniz.')
                                    ->schema([
                                        FileUpload::make('content_attachments')
                                            ->label('Toplu resim / doküman yükle')
                                            ->helperText(fn () => 'JPG, PNG, WEBP, GIF ve PDF desteklenir. Her dosya en fazla ' . ContentAttachmentLimits::maxMegabytes() . ' MB olabilir.')
                                            ->disk('public')
                                            ->directory('announcements/attachments')
                                            ->extraAttributes(['class' => 'content-attachments-upload'])
                                            ->getUploadedFileNameForStorageUsing(fn ($file): string => ContentAttachmentFilenames::forUploadedFile($file, 'announcements/attachments'))
                                            ->deleteUploadedFileUsing(function ($file, $record = null): void {
                                                if (! is_string($file)) {
                                                    return;
                                                }

                                                $asset = MediaAsset::query()
                                                    ->where('disk', 'public')
                                                    ->where('path', $file)
                                                    ->when($record, fn ($query) => $query
                                                        ->where('attachable_type', $record::class)
                                                        ->where('attachable_id', $record->getKey()))
                                                    ->first();

                                                if ($asset) {
                                                    $asset->delete();
                                                }

                                                if ($record && isset($record->content)) {
                                                    $cleaned = ContentHtml::removeReferencesToStoragePath(
                                                        (string) $record->content,
                                                        $file,
                                                        $asset?->url,
                                                    );

                                                    if ($cleaned !== (string) $record->content) {
                                                        $record->forceFill(['content' => $cleaned])->save();
                                                    }
                                                }

                                                if (Storage::disk('public')->exists($file)) {
                                                    Storage::disk('public')->delete($file);
                                                }
                                            })
                                            ->visibility('public')
                                            ->multiple()
                                            ->reorderable()
                                            ->acceptedFileTypes([
                                                'image/jpeg',
                                                'image/png',
                                                'image/webp',
                                                'image/gif',
                                                'application/pdf',
                                            ])
                                            ->maxSize(fn () => ContentAttachmentLimits::maxKilobytes())
                                            ->previewable(false)
                                            ->fetchFileInformation(false)
                                            ->downloadable()
                                            ->openable()
                                            ->dehydrated(),

                                        Hidden::make('deleted_content_attachment_ids')
                                            ->default([])
                                            ->dehydrated(),

                                        View::make('filament.forms.content-files-table'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Yayın')
                            ->schema([
                                Section::make('Yayın durumu')
                                    ->description('Manşet, yorum ve aktiflik ayarları.')
                                    ->schema([
                                        Toggle::make('is_headline')
                                            ->label('Manşette Göster')
                                            ->helperText('Ana sayfa ve ilan listesinde öne çıkarır.'),

                                        Toggle::make('comments_enabled')
                                            ->label('Yorumlara Açık')
                                            ->helperText('Mevcut yorum moderasyon akışı korunarak yorum formunu gösterir.')
                                            ->default(true),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->helperText('Pasif ilanlar public listelerde gösterilmez.')
                                            ->default(true),
                                    ])
                                    ->columns(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
