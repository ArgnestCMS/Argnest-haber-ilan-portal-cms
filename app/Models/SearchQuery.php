<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    protected $fillable = [
        'query',
        'normalized_query',
        'hits',
        'last_searched_at',
        'metadata',
    ];

    protected $casts = [
        'hits' => 'integer',
        'last_searched_at' => 'datetime',
        'metadata' => 'array',
    ];

    public static function record(string $query, array $metadata = []): void
    {
        $normalized = self::normalize($query);

        if ($normalized === '') {
            return;
        }

        $record = self::query()->firstOrNew([
            'normalized_query' => $normalized,
        ]);

        $record->query = mb_substr(trim($query), 0, 120);
        $record->hits = ((int) $record->hits) + 1;
        $record->last_searched_at = now();
        $record->metadata = array_merge($record->metadata ?? [], $metadata);
        $record->save();
    }

    public static function normalize(string $query): string
    {
        return str($query)
            ->lower()
            ->squish()
            ->limit(120, '')
            ->toString();
    }
}
