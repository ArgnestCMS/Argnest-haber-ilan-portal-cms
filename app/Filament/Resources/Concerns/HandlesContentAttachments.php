<?php

namespace App\Filament\Resources\Concerns;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait HandlesContentAttachments
{
    protected array $pendingContentAttachmentPaths = [];

    protected array $deletedContentAttachmentIds = [];

    protected array $contentAttachmentDebug = [];

    protected function extractContentAttachments(array $data): array
    {
        $rawState = $data['content_attachments'] ?? [];
        $rawItems = $this->flattenContentAttachmentState($rawState);

        $this->pendingContentAttachmentPaths = $this->collectContentAttachmentPaths($rawState);

        $this->deletedContentAttachmentIds = array_values(array_filter(
            Arr::wrap($data['deleted_content_attachment_ids'] ?? []),
            fn ($id): bool => is_numeric($id),
        ));

        $this->contentAttachmentDebug = [
            'raw_count' => count($rawItems),
            'raw_items' => array_map(
                fn (mixed $item): mixed => $this->describeContentAttachmentStateItem($item),
                $rawItems,
            ),
            'pending_count' => count($this->pendingContentAttachmentPaths),
            'pending_paths' => $this->pendingContentAttachmentPaths,
            'livewire_tmp_count' => count(array_filter(
                $rawItems,
                fn (mixed $path): bool => is_string($path) && str_contains($path, 'livewire-tmp'),
            )),
            'normalized_skips' => array_values(array_filter(array_map(
                fn (mixed $path): ?array => is_string($path) ? $this->contentAttachmentSkipReason($path) : null,
                $rawItems,
            ))),
            'deleted_ids' => $this->deletedContentAttachmentIds,
            'processed' => [],
            'skipped' => [],
        ];

        unset($data['content_attachments']);
        unset($data['deleted_content_attachment_ids']);

        return $data;
    }

    protected function removeContentAttachmentUploadStateFromFormData(array $data): array
    {
        unset($data['content_attachments']);
        unset($data['deleted_content_attachment_ids']);

        return $data;
    }

    protected function attachPendingContentUploads(Model $record, string $collection): void
    {
        $debug = [
            ...$this->contentAttachmentDebug,
            'record_type' => $record::class,
            'record_id' => $record->getKey(),
            'collection' => $collection,
        ];

        $this->deletePendingContentAttachments($record, $collection, $debug);

        foreach ($this->pendingContentAttachmentPaths as $path) {
            if (! is_string($path) || $path === '') {
                $debug['skipped'][] = [
                    'path' => $path,
                    'reason' => 'not_string_or_empty',
                ];

                continue;
            }

            if (! Storage::disk('public')->exists($path)) {
                $debug['skipped'][] = [
                    'path' => $path,
                    'reason' => 'storage_missing',
                ];

                continue;
            }

            try {
                $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
                $absolutePath = Storage::disk('public')->path($path);
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $size = Storage::disk('public')->size($path);
                $checksum = hash_file('sha256', $absolutePath);
            } catch (Throwable $exception) {
                $debug['skipped'][] = [
                    'path' => $path,
                    'reason' => 'metadata_failed',
                    'error' => $exception->getMessage(),
                ];

                continue;
            }

            $dimensions = str_starts_with($mimeType, 'image/')
                ? @getimagesize($absolutePath)
                : null;

            $attributes = [
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
                'checksum' => $checksum,
                'status' => 'ready',
                'metadata' => [
                    'uploaded_via' => 'filament_content_attachments',
                ],
            ];

            $asset = MediaAsset::withTrashed()
                ->where('attachable_type', $record::class)
                ->where('attachable_id', $record->getKey())
                ->where('collection', $collection)
                ->where('path', $path)
                ->first();

            $action = 'created';

            if ($asset) {
                $action = $asset->trashed() ? 'restored' : 'updated';

                if ($asset->trashed()) {
                    $asset->restore();
                }

                $asset->forceFill($attributes)->save();
            } else {
                $asset = MediaAsset::query()->create([
                    'attachable_type' => $record::class,
                    'attachable_id' => $record->getKey(),
                    'path' => $path,
                    ...$attributes,
                ]);
            }

            $debug['processed'][] = [
                'path' => $path,
                'asset_id' => $asset->getKey(),
                'action' => $action,
                'deleted_at' => $asset->deleted_at,
                'size' => $size,
            ];
        }

        $debug['active_after_save'] = MediaAsset::query()
            ->where('attachable_type', $record::class)
            ->where('attachable_id', $record->getKey())
            ->where('collection', $collection)
            ->get(['id', 'attachable_type', 'attachable_id', 'path', 'deleted_at'])
            ->map(fn (MediaAsset $asset): array => [
                'id' => $asset->id,
                'attachable_type' => $asset->attachable_type,
                'attachable_id' => $asset->attachable_id,
                'path' => $asset->path,
                'deleted_at' => $asset->deleted_at,
            ])
            ->all();

        $debug['active_after_save_count'] = count($debug['active_after_save']);

        $this->clearContentAttachmentUploadState();

        $this->pendingContentAttachmentPaths = [];
        $this->deletedContentAttachmentIds = [];
        $this->contentAttachmentDebug = [];
    }

    protected function clearContentAttachmentUploadState(): void
    {
        if (! property_exists($this, 'data') || ! is_array($this->data)) {
            return;
        }

        $this->data['content_attachments'] = [];
        $this->data['deleted_content_attachment_ids'] = [];

    }

    protected function collectContentAttachmentPaths(mixed $state): array
    {
        $paths = [];

        foreach (Arr::wrap($state) as $item) {
            if (is_array($item)) {
                array_push($paths, ...$this->collectContentAttachmentPaths($item));

                continue;
            }

            if (! is_string($item) || $item === '') {
                continue;
            }

            $path = $this->normalizeContentAttachmentPath($item);

            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    protected function flattenContentAttachmentState(mixed $state): array
    {
        $items = [];

        foreach (Arr::wrap($state) as $item) {
            if (is_array($item)) {
                array_push($items, ...$this->flattenContentAttachmentState($item));

                continue;
            }

            if ($item !== null && $item !== '') {
                $items[] = $item;
            }
        }

        return $items;
    }

    protected function describeContentAttachmentStateItem(mixed $item): mixed
    {
        if (is_object($item)) {
            return [
                'type' => $item::class,
            ];
        }

        if (is_scalar($item) || $item === null) {
            return $item;
        }

        return [
            'type' => gettype($item),
        ];
    }

    protected function normalizeContentAttachmentPath(string $path): ?string
    {
        $path = trim($path);
        $storagePrefix = '/storage/';

        if (str_starts_with($path, $storagePrefix)) {
            $path = substr($path, strlen($storagePrefix));
        }

        if (str_contains($path, 'livewire-tmp') || str_contains($path, '..')) {
            return null;
        }

        return ltrim($path, '/');
    }

    protected function contentAttachmentSkipReason(string $path): ?array
    {
        $path = trim($path);

        if (str_contains($path, 'livewire-tmp')) {
            return [
                'path' => $path,
                'reason' => 'livewire_tmp_not_final_path',
            ];
        }

        if (str_contains($path, '..')) {
            return [
                'path' => $path,
                'reason' => 'unsafe_relative_path',
            ];
        }

        return null;
    }

    protected function deletePendingContentAttachments(Model $record, string $collection, array &$debug): void
    {
        if ($this->deletedContentAttachmentIds === []) {
            return;
        }

        $assets = MediaAsset::query()
            ->whereKey($this->deletedContentAttachmentIds)
            ->where('attachable_type', $record::class)
            ->where('attachable_id', $record->getKey())
            ->where('collection', $collection)
            ->get();

        foreach ($assets as $asset) {
            $debug['deleted'][] = [
                'asset_id' => $asset->getKey(),
                'path' => $asset->path,
                'storage_existed' => Storage::disk($asset->disk)->exists($asset->path),
            ];

            $this->removeContentAttachmentFromRecordContent($record, $asset);
            $this->deleteStoredContentAttachmentFiles($asset);
            $asset->delete();
        }
    }

    protected function removeContentAttachmentFromRecordContent(Model $record, MediaAsset $asset): void
    {
        if (! isset($record->content)) {
            return;
        }

        $content = (string) $record->content;
        $cleaned = $this->removeContentAttachmentTags($content, $asset);

        if ($cleaned !== $content) {
            $record->forceFill(['content' => $cleaned])->save();
        }
    }

    protected function removeContentAttachmentTags(string $content, MediaAsset $asset): string
    {
        $urls = array_values(array_filter(array_unique([
            $asset->path,
            '/storage/' . ltrim($asset->path, '/'),
            $asset->url,
            asset('storage/' . ltrim($asset->path, '/')),
        ])));

        foreach ($urls as $url) {
            $quotedUrl = preg_quote($url, '~');
            $content = preg_replace('~<p>\s*<img\b[^>]*\bsrc=["\']' . $quotedUrl . '["\'][^>]*>\s*</p>~iu', '', $content) ?? $content;
            $content = preg_replace('~<img\b[^>]*\bsrc=["\']' . $quotedUrl . '["\'][^>]*>~iu', '', $content) ?? $content;
            $content = preg_replace('~<p>\s*<a\b[^>]*\bhref=["\']' . $quotedUrl . '["\'][^>]*>.*?</a>\s*</p>~isu', '', $content) ?? $content;
            $content = preg_replace('~<a\b[^>]*\bhref=["\']' . $quotedUrl . '["\'][^>]*>.*?</a>~isu', '', $content) ?? $content;
        }

        return trim($content);
    }

    protected function deleteStoredContentAttachmentFiles(MediaAsset $asset): void
    {
        foreach (array_filter([$asset->path, $asset->thumbnail_path]) as $path) {
            if (Storage::disk($asset->disk)->exists($path)) {
                Storage::disk($asset->disk)->delete($path);
            }
        }
    }
}
