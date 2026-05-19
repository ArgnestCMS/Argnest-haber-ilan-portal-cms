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

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }
}
