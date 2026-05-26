<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\News\NewsResource;
use App\Models\Announcement;
use App\Models\MediaAsset;
use App\Models\News;
use App\Support\ContentAttachmentFilenames;
use App\Support\ContentAttachmentLimits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentAttachmentController extends Controller
{
    public function update(Request $request, MediaAsset $mediaAsset): RedirectResponse | JsonResponse
    {
        $attachable = $mediaAsset->attachable;

        abort_unless($this->isEditableContentAttachment($mediaAsset, $attachable), 403);

        $data = $request->validate([
            'file' => [
                'required',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf',
                'max:' . ContentAttachmentLimits::maxKilobytes($request->user()),
            ],
        ]);

        $file = $data['file'];
        $directory = $this->directoryForCollection($mediaAsset->collection);
        $filename = ContentAttachmentFilenames::forUploadedFile($file, $directory, $mediaAsset->path, 'file');
        $path = $file->storeAs($directory, $filename, 'public');
        $absolutePath = Storage::disk('public')->path($path);
        $mimeType = Storage::disk('public')->mimeType($path) ?: $file->getMimeType();
        $dimensions = str_starts_with($mimeType, 'image/')
            ? @getimagesize($absolutePath)
            : null;

        if ($path !== $mediaAsset->path && Storage::disk('public')->exists($mediaAsset->path)) {
            Storage::disk('public')->delete($mediaAsset->path);
        }

        $mediaAsset->update([
            'user_id' => auth()->id(),
            'original_name' => basename($path),
            'file_name' => basename($path),
            'path' => $path,
            'thumbnail_path' => null,
            'mime_type' => $mimeType,
            'extension' => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            'size' => Storage::disk('public')->size($path),
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'checksum' => hash_file('sha256', $absolutePath),
            'status' => 'ready',
            'metadata' => array_merge($mediaAsset->metadata ?? [], [
                'replaced_via' => 'filament_content_attachments',
                'replaced_at' => now()->toISOString(),
            ]),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $mediaAsset->id,
                'url' => $mediaAsset->url,
                'path' => $mediaAsset->path,
                'name' => $mediaAsset->original_name,
            ]);
        }

        return back()->with('success', 'Dosya değiştirildi.');
    }

    public function destroy(Request $request, MediaAsset $mediaAsset): RedirectResponse | JsonResponse
    {
        $id = $mediaAsset->id;
        $attachable = $mediaAsset->attachable;

        abort_unless($this->isEditableContentAttachment($mediaAsset, $attachable), 403);

        $this->removeAttachmentFromContent($attachable, $mediaAsset);
        $this->deleteStoredAttachmentFiles($mediaAsset);

        $mediaAsset->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'deleted' => true,
                'id' => $id,
            ]);
        }

        return back()->with('success', 'Dosya listeden kaldırıldı.');
    }

    private function isEditableContentAttachment(MediaAsset $mediaAsset, mixed $attachable): bool
    {
        if ($attachable instanceof News) {
            return $mediaAsset->collection === 'news_attachment'
                && NewsResource::canEdit($attachable);
        }

        if ($attachable instanceof Announcement) {
            return $mediaAsset->collection === 'announcement_attachment'
                && AnnouncementResource::canEdit($attachable);
        }

        return false;
    }

    private function removeAttachmentFromContent(mixed $attachable, MediaAsset $mediaAsset): void
    {
        if (! $attachable instanceof News && ! $attachable instanceof Announcement) {
            return;
        }

        $content = (string) ($attachable->content ?? '');
        $cleaned = $this->removeAttachmentTags($content, $mediaAsset);

        if ($cleaned !== $content) {
            $attachable->forceFill(['content' => $cleaned])->save();
        }
    }

    private function removeAttachmentTags(string $content, MediaAsset $mediaAsset): string
    {
        $urls = array_values(array_filter(array_unique([
            $mediaAsset->path,
            '/storage/' . ltrim($mediaAsset->path, '/'),
            $mediaAsset->url,
            asset('storage/' . ltrim($mediaAsset->path, '/')),
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

    private function deleteStoredAttachmentFiles(MediaAsset $mediaAsset): void
    {
        foreach (array_filter([$mediaAsset->path, $mediaAsset->thumbnail_path]) as $path) {
            if (Storage::disk($mediaAsset->disk)->exists($path)) {
                Storage::disk($mediaAsset->disk)->delete($path);
            }
        }
    }

    private function directoryForCollection(string $collection): string
    {
        return match ($collection) {
            'news_attachment' => 'news/attachments',
            'announcement_attachment' => 'announcements/attachments',
            default => abort(403),
        };
    }
}
