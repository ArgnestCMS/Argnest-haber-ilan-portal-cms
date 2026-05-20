<?php

namespace App\Filament\Resources\News\Schemas;

use App\Models\Category;
use App\Models\MediaAsset;
use App\Support\ContentAttachmentFilenames;
use App\Support\ContentAttachmentLimits;
use App\Support\ContentHtml;
use Filament\Forms\Components\DateTimePicker;
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

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Haber editörü')
                    ->tabs([
                        Tabs\Tab::make('İçerik')
                            ->schema([
                                Section::make('Temel bilgiler')
                                    ->description('Başlık, URL, kategori ve yayın tarihini buradan yönetin.')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Başlık')
                                            ->helperText('Liste, manşet ve SEO başlığı için ana metin. Net ve aranabilir tutun.')
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('slug', Str::slug($state));
                                            })
                                            ->required(),

                                        TextInput::make('slug')
                                            ->label('URL (Slug)')
                                            ->helperText('Başlıktan otomatik oluşur. Gerekirse kısa ve kalıcı bir URL olacak şekilde düzenleyin.')
                                            ->maxLength(255)
                                            ->required(),

                                        Select::make('category_id')
                                            ->label('Kategori')
                                            ->helperText('Haberin listeleneceği aktif haber kategorisi.')
                                            ->options(fn () => Category::where('type', 'news')
                                                ->where('is_active', true)
                                                ->orderBy('sort_order')
                                                ->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        TextInput::make('source')
                                            ->label('Kaynak')
                                            ->helperText('Varsa haber kaynağını veya kurum adını yazın.')
                                            ->maxLength(255),

                                        DateTimePicker::make('publish_date')
                                            ->label('Yayın Tarihi')
                                            ->helperText('Boş bırakılırsa içerik mevcut yayın akışına göre değerlendirilir.')
                                            ->seconds(false),

                                        DateTimePicker::make('end_date')
                                            ->label('Bitiş Tarihi')
                                            ->helperText('Zamana bağlı içerikler için opsiyonel bitiş tarihi.')
                                            ->seconds(false)
                                            ->nullable(),
                                    ])
                                    ->columns(2),

                                Section::make('Özet ve haber metni')
                                    ->description('Kartlarda görünen kısa özet ve ana haber içeriği.')
                                    ->schema([
                                        Textarea::make('summary')
                                            ->label('Kısa Açıklama')
                                            ->helperText('Kartlarda ve meta açıklamada kullanılır. 140-160 karakter idealdir.')
                                            ->rows(5)
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        RichEditor::make('content')
                                            ->label('Haber İçeriği')
                                            ->helperText('Okuma deneyimi için ara başlıklar ve kısa paragraflar tercih edin.')
                                            ->required()
                                            ->extraInputAttributes([
                                                'style' => 'min-height: 500px;',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Yayın')
                            ->schema([
                                Section::make('Yayın ve görünürlük')
                                    ->description('Manşet, yorum ve editoryal vurgu ayarları.')
                                    ->schema([
                                        Select::make('news_type')
                                            ->label('Haber Türü')
                                            ->helperText('Editoryal vurgu tipini seçin; manşet gösterimi ayrı anahtar ile kontrol edilir.')
                                            ->options([
                                                'normal' => 'Normal',
                                                'manset' => 'Manşet',
                                                'son_dakika' => 'Son Dakika',
                                            ])
                                            ->default('normal'),

                                        Toggle::make('is_headline')
                                            ->label('Manşete Göster')
                                            ->helperText('Ana sayfa manşet alanında öne çıkarır.'),

                                        Toggle::make('comments_enabled')
                                            ->label('Yorumlara Açık')
                                            ->helperText('Okuyucu yorum formunu gösterir; mevcut moderasyon akışı korunur.')
                                            ->default(true),

                                        TextInput::make('views')
                                            ->label('Görüntülenme')
                                            ->helperText('Genellikle sistem tarafından artar; gerekirse düzeltme için kullanın.')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(2),

                                Section::make('Sosyal hazırlık')
                                    ->description('Sosyal paylaşım planı için editoryal işaretler.')
                                    ->schema([
                                        Toggle::make('share_facebook')
                                            ->label('Facebook için yayınla')
                                            ->helperText('Sosyal paylaşım hazırlığı için editoryal işaret.'),

                                        Toggle::make('share_twitter')
                                            ->label('Twitter/X için yayınla')
                                            ->helperText('X/Twitter paylaşım hazırlığı için editoryal işaret.'),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Medya')
                            ->schema([
                                Section::make('Haber görseli')
                                    ->description('Manşet ve liste kartlarında kullanılacak ana görsel.')
                                    ->schema([
                                        View::make('filament.forms.components.news-image-preview'),

                                        FileUpload::make('image')
                                            ->label('Haber Görseli')
                                            ->helperText('Yatay, net ve 1200px+ genişlikte JPG, PNG veya WEBP görsel tercih edin.')
                                            ->image()
                                            ->disk('public')
                                            ->directory('news')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->maxSize(5120)
                                            ->previewable(false)
                                            ->downloadable()
                                            ->openable(),
                                    ]),

                                Section::make('Ek doküman')
                                    ->description('Varsa habere bağlı PDF dokümanı.')
                                    ->schema([
                                        FileUpload::make('document')
                                            ->label('PDF / Doküman')
                                            ->helperText('Varsa ek belge yükleyin. Haber görselinden ayrı tutulur.')
                                            ->disk('public')
                                            ->directory('news/documents')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(fn () => ContentAttachmentLimits::maxKilobytes())
                                            ->previewable(false)
                                            ->downloadable()
                                            ->openable(),
                                    ]),

                                Section::make('Toplu içerik dosyaları')
                                    ->description('Habere bağlı birden fazla görsel veya dokümanı yükleyin; kayıttan sonra listeden kopyalayabilir ya da içeriğe ekleyebilirsiniz.')
                                    ->schema([
                                        FileUpload::make('content_attachments')
                                            ->label('Toplu resim / doküman yükle')
                                            ->helperText(fn () => 'JPG, PNG, WEBP, GIF ve PDF desteklenir. Her dosya en fazla ' . ContentAttachmentLimits::maxMegabytes() . ' MB olabilir.')
                                            ->disk('public')
                                            ->directory('news/attachments')
                                            ->extraAttributes(['class' => 'content-attachments-upload'])
                                            ->getUploadedFileNameForStorageUsing(fn ($file): string => ContentAttachmentFilenames::forUploadedFile($file, 'news/attachments'))
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
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
