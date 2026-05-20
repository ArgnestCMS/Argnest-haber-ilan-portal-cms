<?php

namespace App\Filament\Resources\Concerns;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait HandlesContentAttachments
{
    protected array $pendingContentAttachmentPaths = [];

    protected function extractContentAttachments(array $data): array
    {
        $this->pendingContentAttachmentPaths = array_values(array_filter(
            Arr::wrap($data['content_attachments'] ?? [])
        ));

        unset($data['content_attachments']);

        return $data;
    }

    protected function attachPendingContentUploads(Model $record, string $collection): void
    {
        foreach ($this->pendingContentAttachmentPaths as $path) {
            if (! is_string($path) || ! Storage::disk('public')->exists($path)) {
                continue;
            }

            $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
            $absolutePath = Storage::disk('public')->path($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $size = Storage::disk('public')->size($path);
            $dimensions = str_starts_with($mimeType, 'image/')
                ? @getimagesize($absolutePath)
                : null;

            MediaAsset::query()->firstOrCreate(
                [
                    'attachable_type' => $record::class,
                    'attachable_id' => $record->getKey(),
                    'path' => $path,
                ],
                [
                    'user_id' => auth()->id(),
                    'collection' => $collection,
                    'disk' => 'public',
                    'visibility' => 'public',
                    'original_name' => basename($path),
                    'file_name' => basename($path),
                    'thumbnail_path' => null,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'size' => $size,
                    'width' => $dimensions[0] ?? null,
                    'height' => $dimensions[1] ?? null,
                    'checksum' => hash_file('sha256', $absolutePath),
                    'status' => 'ready',
                    'metadata' => [
                        'uploaded_via' => 'filament_content_attachments',
                    ],
                ],
            );
        }

        $this->pendingContentAttachmentPaths = [];
    }
}
