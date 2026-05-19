<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'attachable_type',
        'attachable_id',
        'collection',
        'disk',
        'visibility',
        'original_name',
        'file_name',
        'path',
        'thumbnail_path',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'checksum',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    protected $appends = [
        'url',
        'thumbnail_url',
        'human_size',
        'is_orphan',
        'is_large',
        'storage_missing',
        'thumbnail_missing',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->visibility !== 'public') {
            return null;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->visibility === 'public' && $this->thumbnail_path
            ? Storage::disk($this->disk)->url($this->thumbnail_path)
            : null;
    }

    public function getHumanSizeAttribute(): string
    {
        if ($this->size >= 1024 * 1024) {
            return round($this->size / 1024 / 1024, 2) . ' MB';
        }

        return round($this->size / 1024, 1) . ' KB';
    }

    public function getIsOrphanAttribute(): bool
    {
        return ! $this->attachable_type || ! $this->attachable_id;
    }

    public function getIsLargeAttribute(): bool
    {
        return $this->size >= ((int) config('media.management.large_file_warning_mb', 20) * 1024 * 1024);
    }

    public function getStorageMissingAttribute(): bool
    {
        return ! Storage::disk($this->disk)->exists($this->path);
    }

    public function getThumbnailMissingAttribute(): bool
    {
        return $this->thumbnail_path !== null
            && ! Storage::disk($this->disk)->exists($this->thumbnail_path);
    }

    public function markSuspicious(?string $note = null): void
    {
        $metadata = $this->metadata ?? [];
        $metadata['suspicious_marked_at'] = now()->toISOString();
        $metadata['suspicious_marked_by'] = auth()->id();

        if ($note) {
            $metadata['suspicious_note'] = $note;
        }

        $this->update([
            'status' => 'suspicious',
            'metadata' => $metadata,
        ]);
    }

    public function markReady(): void
    {
        $metadata = $this->metadata ?? [];
        unset($metadata['suspicious_marked_at'], $metadata['suspicious_marked_by'], $metadata['suspicious_note']);

        $this->update([
            'status' => 'ready',
            'metadata' => $metadata,
        ]);
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeOrphan($query)
    {
        return $query
            ->whereNull('attachable_type')
            ->whereNull('attachable_id');
    }
}
