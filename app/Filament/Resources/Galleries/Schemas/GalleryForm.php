<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Section::make('Galeri Bilgileri')

                    ->schema([

                        TextInput::make('title')
                            ->label('Galeri Başlığı')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        FileUpload::make('cover_image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('public')
                            ->directory('gallery-covers'),

                        RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make('Galeri Resimleri')

                    ->schema([

                        Repeater::make('images')

                            ->relationship()

                            ->schema([

                                FileUpload::make('image')
                                    ->label('Resim')
                                    ->image()
                                    ->disk('public')
                                    ->directory('gallery-images')
                                    ->imagePreviewHeight('110')
                                    ->panelLayout('compact')
                                    ->required()
                                    ->columnSpan([
                                        'default' => 6,
                                        'md' => 2,
                                    ]),

                                TextInput::make('title')
                                    ->label('Başlık')
                                    ->placeholder('İsteğe bağlı görsel başlığı')
                                    ->columnSpan([
                                        'default' => 6,
                                        'md' => 2,
                                    ]),

                                TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan([
                                        'default' => 3,
                                        'md' => 1,
                                    ]),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->columnSpan([
                                        'default' => 3,
                                        'md' => 1,
                                    ]),

                                Checkbox::make('_selected_for_delete')
                                    ->label('Toplu silme için seç')
                                    ->dehydrated(false)
                                    ->live()
                                    ->columnSpanFull(),

                            ])

                            ->columns(6)

                            ->collapsible()

                            ->cloneable()

                            ->reorderable()

                            ->reorderableWithButtons()

                            ->itemLabel(fn (array $state): ?string => filled($state['title'] ?? null)
                                ? $state['title']
                                : 'Galeri resmi')

                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading('Bu görsel silinsin mi?')
                                    ->modalDescription('Görsel satırı formdan kaldırılacak. Kalıcı işlem kaydettiğinizde uygulanır.')
                                    ->modalSubmitActionLabel('Evet, sil')
                            ),

                    ])

                    ->headerActions([
                        Action::make('deleteSelectedGalleryImages')
                            ->label('Seçili resimleri sil')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Seçili resimler silinsin mi?')
                            ->modalDescription('Seçili görsel satırları formdan kaldırılacak. Kalıcı işlem kaydettiğinizde uygulanır.')
                            ->modalSubmitActionLabel('Seçili resimleri sil')
                            ->disabled(fn (Get $get): bool => ! collect($get('images') ?? [])
                                ->contains(fn (array $image): bool => (bool) ($image['_selected_for_delete'] ?? false)))
                            ->action(function (Get $get, Set $set): void {
                                $images = collect($get('images') ?? [])
                                    ->reject(fn (array $image): bool => (bool) ($image['_selected_for_delete'] ?? false))
                                    ->map(function (array $image): array {
                                        unset($image['_selected_for_delete']);

                                        return $image;
                                    })
                                    ->values()
                                    ->all();

                                $set('images', $images);
                            }),

                        Action::make('deleteAllGalleryImages')
                            ->label('Tümünü sil')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Tüm galeri resimleri silinsin mi?')
                            ->modalDescription('Tüm görsel satırları formdan kaldırılacak. Kalıcı işlem kaydettiğinizde uygulanır.')
                            ->modalSubmitActionLabel('Tümünü sil')
                            ->disabled(fn (Get $get): bool => blank($get('images')))
                            ->action(function (Set $set): void {
                                $set('images', []);
                            }),
                    ]),

                Section::make('Yayın Ayarları')

                    ->schema([

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),

                        DateTimePicker::make('published_at')
                            ->label('Yayın Tarihi'),

                    ])

                    ->columns(3),

            ]);
    }
}
