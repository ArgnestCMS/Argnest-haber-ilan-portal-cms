<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MediaAssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('thumbnail_url')
                    ->label('Onizleme')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('original_name')->label('Orijinal Ad'),
                TextEntry::make('file_name')->label('Dosya Adi'),
                TextEntry::make('owner.name')->label('Kullanici')->placeholder('Sistem'),
                TextEntry::make('collection')->label('Koleksiyon')->badge(),
                TextEntry::make('status')->label('Durum')->badge(),
                TextEntry::make('visibility')->label('Gorunurluk')->badge(),
                TextEntry::make('disk')->label('Disk')->badge(),
                TextEntry::make('mime_type')->label('Mime')->badge(),
                TextEntry::make('extension')->label('Uzanti')->badge(),
                TextEntry::make('human_size')->label('Boyut'),
                TextEntry::make('width')->label('Genislik')->numeric()->placeholder('-'),
                TextEntry::make('height')->label('Yukseklik')->numeric()->placeholder('-'),
                IconEntry::make('is_large')->label('Buyuk Medya')->boolean(),
                IconEntry::make('is_orphan')->label('Orphan')->boolean(),
                IconEntry::make('storage_missing')->label('Storage Dosyasi Eksik')->boolean(),
                IconEntry::make('thumbnail_missing')->label('Thumbnail Eksik')->boolean(),
                TextEntry::make('attachable_type')->label('Bagli Model')->placeholder('-'),
                TextEntry::make('attachable_id')->label('Bagli Kayit')->numeric()->placeholder('-'),
                TextEntry::make('path')->label('Path')->copyable()->columnSpanFull(),
                TextEntry::make('thumbnail_path')->label('Thumbnail Path')->copyable()->placeholder('-')->columnSpanFull(),
                TextEntry::make('checksum')->label('Checksum')->copyable()->columnSpanFull(),
                TextEntry::make('metadata')->label('Metadata')->json()->placeholder('-')->columnSpanFull(),
                TextEntry::make('created_at')->label('Yukleme')->dateTime('d.m.Y H:i')->placeholder('-'),
                TextEntry::make('updated_at')->label('Guncelleme')->dateTime('d.m.Y H:i')->placeholder('-'),
                TextEntry::make('deleted_at')->label('Soft Delete')->dateTime('d.m.Y H:i')->placeholder('-'),
            ]);
    }
}
