<?php

namespace App\Filament\Resources\CommunityReports\Tables;

use App\Helpers\NotificationHelper;
use App\Models\CommunityReport;
use App\Models\ForumPost;
use App\Models\ForumTopic;
use App\Models\LiveChatMessage;
use App\Support\ForumGamification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommunityReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['reporter', 'reviewer', 'reportable'])
                ->orderByRaw("case status when 'pending' then 0 when 'open' then 1 else 2 end")
                ->orderByDesc('subject_ai_risk_score')
                ->latest())
            ->columns([
                TextColumn::make('reporter.name')
                    ->label('Raporlayan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reporter.community_trust_score')
                    ->label('Guven')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reportable_label')
                    ->label('Icerik')
                    ->getStateUsing(fn (CommunityReport $record) => $record->reportableLabel())
                    ->badge(),

                TextColumn::make('subject')
                    ->label('Baslik / Mesaj')
                    ->getStateUsing(fn (CommunityReport $record) => $record->subjectTitle())
                    ->limit(60)
                    ->wrap()
                    ->searchable(false),

                TextColumn::make('reason')
                    ->label('Sebep')
                    ->formatStateUsing(fn ($state) => CommunityReport::REASONS[$state] ?? $state)
                    ->badge(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->formatStateUsing(fn ($state) => CommunityReport::STATUSES[$state] ?? $state)
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'open' => 'info',
                        'resolved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('subject_ai_risk_label')
                    ->label('AI Risk')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('subject_ai_risk_score')
                    ->label('Risk')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options(CommunityReport::STATUSES),

                SelectFilter::make('reason')
                    ->label('Sebep')
                    ->options(CommunityReport::REASONS),

                SelectFilter::make('subject_ai_risk_label')
                    ->label('AI Risk')
                    ->options([
                        'critical' => 'Kritik',
                        'high' => 'Yuksek',
                        'medium' => 'Orta',
                        'low' => 'Dusuk',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Goruntule'),
                EditAction::make()->label('Duzenle'),

                Action::make('view_subject')
                    ->label('Icerige Git')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (CommunityReport $record) => $record->subjectUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (CommunityReport $record) => filled($record->subjectUrl())),

                Action::make('open')
                    ->label('Ac')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn (CommunityReport $record) => self::mark($record, 'open')),

                Action::make('resolve')
                    ->label('Coz')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'resolved')
                    ->form([
                        Select::make('resolution_action')
                            ->label('Icerik Aksiyonu')
                            ->options([
                                'none' => 'Sadece raporu coz',
                                'hide' => 'Icerigi gizle/reddet',
                                'delete' => 'Icerigi sil',
                            ])
                            ->default('none')
                            ->required(),

                        Textarea::make('moderator_note')
                            ->label('Moderator Notu')
                            ->rows(4),
                    ])
                    ->action(function (CommunityReport $record, array $data) {
                        self::applySubjectAction($record, $data['resolution_action'] ?? 'none', $data['moderator_note'] ?? null);
                        self::mark($record, 'resolved', $data['moderator_note'] ?? null, $data['resolution_action'] ?? 'none');
                        self::adjustTrust($record, 5, 'report_resolved');
                        self::notifyReporter($record, 'resolved');
                    }),

                Action::make('reviewed')
                    ->label('Incelendi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('gray')
                    ->form([
                        Textarea::make('moderator_note')
                            ->label('Moderator Notu')
                            ->rows(4),
                    ])
                    ->action(function (CommunityReport $record, array $data) {
                        self::mark($record, 'reviewed', $data['moderator_note'] ?? null);
                        self::notifyReporter($record, 'reviewed');
                    }),

                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->form([
                        Textarea::make('moderator_note')
                            ->label('Red Sebebi')
                            ->rows(4),
                    ])
                    ->action(function (CommunityReport $record, array $data) {
                        self::mark($record, 'rejected', $data['moderator_note'] ?? null);
                        self::adjustTrust($record, -5, 'report_rejected');
                        self::notifyReporter($record, 'rejected');
                    }),

                DeleteAction::make()->label('Sil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Secilenleri Sil'),
                ]),
            ]);
    }

    private static function mark(CommunityReport $record, string $status, ?string $note = null, ?string $action = null): void
    {
        $record->update([
            'status' => $status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'moderator_note' => $note,
            'resolution_action' => $action,
        ]);
    }

    private static function applySubjectAction(CommunityReport $record, string $action, ?string $note): void
    {
        $subject = $record->reportable;

        if ($action === 'none' || ! $subject) {
            return;
        }

        if ($action === 'delete') {
            $subject->delete();

            return;
        }

        if ($subject instanceof ForumTopic) {
            $subject->update(['status' => 'hidden', 'moderator_note' => $note]);
        }

        if ($subject instanceof ForumPost || $subject instanceof LiveChatMessage) {
            $subject->update([
                'status' => 'rejected',
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
                'moderation_note' => $note,
            ]);
        }
    }

    private static function adjustTrust(CommunityReport $record, int $delta, string $event): void
    {
        $reporter = $record->reporter;

        if (! $reporter) {
            return;
        }

        $reputationEvent = ForumGamification::award($reporter, $event, $record, [
            'trust_delta' => $delta,
            'moderator_id' => auth()->id(),
        ]);

        if (! $reputationEvent) {
            return;
        }

        $reporter->forceFill([
            'community_trust_score' => min(100, max(0, (int) $reporter->community_trust_score + $delta)),
        ])->save();
    }

    private static function notifyReporter(CommunityReport $record, string $status): void
    {
        if (! $record->user_id) {
            return;
        }

        NotificationHelper::sendToUser(
            userId: $record->user_id,
            type: 'community_report_' . $status,
            title: 'Raporunuz ' . (CommunityReport::STATUSES[$status] ?? $status),
            message: '"' . $record->subjectTitle() . '" icin gonderdiginiz rapor sonucuyla ilgili islem yapildi.',
            url: route('forum.dashboard'),
            data: [
                'report_id' => $record->id,
                'status' => $status,
            ]
        );
    }
}
