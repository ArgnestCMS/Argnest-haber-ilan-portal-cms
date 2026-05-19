<?php

namespace App\Filament\Resources\CommunityReports\Schemas;

use App\Models\CommunityReport;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommunityReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label('Durum')
                    ->options(CommunityReport::STATUSES)
                    ->required(),

                Select::make('reason')
                    ->label('Sebep')
                    ->options(CommunityReport::REASONS)
                    ->required(),

                Textarea::make('details')
                    ->label('Kullanici Aciklamasi')
                    ->rows(4)
                    ->columnSpanFull(),

                Textarea::make('moderator_note')
                    ->label('Moderator Notu')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
