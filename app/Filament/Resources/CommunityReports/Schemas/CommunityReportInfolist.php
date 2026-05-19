<?php

namespace App\Filament\Resources\CommunityReports\Schemas;

use App\Models\CommunityReport;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommunityReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('reporter.name')->label('Raporlayan')->placeholder('-'),
                TextEntry::make('reporter.community_trust_score')->label('Guven Puani')->numeric()->placeholder('-'),
                TextEntry::make('reportable_label')->label('Icerik Turu')->getStateUsing(fn (CommunityReport $record) => $record->reportableLabel()),
                TextEntry::make('subject')->label('Icerik')->getStateUsing(fn (CommunityReport $record) => $record->subjectTitle())->columnSpanFull(),
                TextEntry::make('reason')->label('Sebep')->formatStateUsing(fn ($state) => CommunityReport::REASONS[$state] ?? $state)->badge(),
                TextEntry::make('status')->label('Durum')->formatStateUsing(fn ($state) => CommunityReport::STATUSES[$state] ?? $state)->badge(),
                TextEntry::make('subject_ai_risk_label')->label('AI Risk')->badge(),
                TextEntry::make('subject_ai_risk_score')->label('AI Risk Puani')->numeric(),
                TextEntry::make('details')->label('Kullanici Aciklamasi')->placeholder('-')->columnSpanFull(),
                TextEntry::make('moderator_note')->label('Moderator Notu')->placeholder('-')->columnSpanFull(),
                TextEntry::make('resolution_action')->label('Aksiyon')->placeholder('-'),
                TextEntry::make('reviewer.name')->label('Inceleyen')->placeholder('-'),
                TextEntry::make('reviewed_at')->label('Inceleme Tarihi')->dateTime('d.m.Y H:i')->placeholder('-'),
                TextEntry::make('created_at')->label('Rapor Tarihi')->dateTime('d.m.Y H:i'),
            ])
            ->columns(2);
    }
}
