<?php

namespace App\Models;

use App\Events\LiveActivityRecorded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LiveActivity extends Model
{
    use SoftDeletes;

    public const SEVERITIES = ['info', 'success', 'warning', 'danger'];

    public const SOURCES = ['auth', 'forum', 'chat', 'system'];

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'type',
        'source',
        'severity',
        'title',
        'message',
        'url',
        'metadata',
        'is_public',
        'is_important',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_important' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('occurred_at')->orderByDesc('id');
    }

    public static function record(array $data): self
    {
        $user = $data['user'] ?? auth()->user();
        $subject = $data['subject'] ?? null;
        $severity = $data['severity'] ?? 'info';
        $source = $data['source'] ?? 'system';

        $activity = self::create([
            'user_id' => $data['user_id'] ?? $user?->id,
            'subject_type' => $subject ? $subject::class : ($data['subject_type'] ?? null),
            'subject_id' => $subject?->getKey() ?? ($data['subject_id'] ?? null),
            'type' => $data['type'],
            'source' => in_array($source, self::SOURCES, true) ? $source : 'system',
            'severity' => in_array($severity, self::SEVERITIES, true) ? $severity : 'info',
            'title' => Str::limit($data['title'], 255, ''),
            'message' => $data['message'] ?? null,
            'url' => $data['url'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'is_public' => $data['is_public'] ?? true,
            'is_important' => $data['is_important'] ?? false,
            'occurred_at' => $data['occurred_at'] ?? now(),
        ]);

        if ($data['broadcast'] ?? true) {
            LiveActivityRecorded::dispatch($activity);
        }

        return $activity;
    }

    public function toFeedItem(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'source' => $this->source,
            'severity' => $this->severity,
            'is_important' => $this->is_important,
            'title' => e($this->title),
            'message' => $this->message ? e($this->message) : null,
            'url' => $this->url,
            'user' => $this->user?->name ? e($this->user->name) : 'Sistem',
            'time' => $this->occurred_at?->format('H:i'),
            'date' => $this->occurred_at?->format('d.m.Y'),
            'relative_time' => $this->occurred_at?->diffForHumans(),
        ];
    }
}
