<?php

namespace App\Support;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaUploadService
{
    private const IMAGE_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    public function storeForumImage(UploadedFile $file, User $user): MediaAsset
    {
        return $this->storeImage($file, $user, 'forum', 'public', 'public');
    }

    public function storeDirectMessageImage(UploadedFile $file, User $user): MediaAsset
    {
        return $this->storeImage($file, $user, 'direct_message', 'local', 'private');
    }

    public function attachForumMedia(array $ids, object $attachable, User $user): void
    {
        $cleanIds = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(8)
            ->values();

        if ($cleanIds->isEmpty()) {
            return;
        }

        MediaAsset::query()
            ->whereIn('id', $cleanIds)
            ->where('user_id', $user->id)
            ->where('collection', 'forum')
            ->where('status', 'ready')
            ->whereNull('attachable_id')
            ->whereNull('attachable_type')
            ->update([
                'attachable_type' => get_class($attachable),
                'attachable_id' => $attachable->id,
                'updated_at' => now(),
            ]);
    }

    private function storeImage(UploadedFile $file, User $user, string $collection, string $disk, string $visibility): MediaAsset
    {
        $uploadLimitBytes = $this->imageUploadLimitBytes($user);

        $this->validateImage($file, $uploadLimitBytes);

        $mime = $file->getMimeType();
        $extension = self::IMAGE_MIMES[$mime];
        $imageSize = getimagesize($file->getRealPath()) ?: [];
        $width = (int) ($imageSize[0] ?? 0);
        $height = (int) ($imageSize[1] ?? 0);
        $checksum = hash_file('sha256', $file->getRealPath());
        $baseName = Str::uuid()->toString();
        $directory = $collection . '/images/' . now()->format('Y/m');
        $thumbnailDirectory = $collection . '/thumbnails/' . now()->format('Y/m');
        $fileName = $baseName . '.' . $extension;
        $path = $directory . '/' . $fileName;
        $thumbnailPath = $thumbnailDirectory . '/' . $baseName . '.jpg';
        $optimized = $this->optimizedImageBytes($file, $mime, 1600, 86);

        Storage::disk($disk)->put($path, $optimized ?? file_get_contents($file->getRealPath()), $visibility);

        $thumbnail = $mime === 'image/gif'
            ? null
            : $this->optimizedImageBytes($file, $mime, 480, 78, 'image/jpeg');

        if ($thumbnail) {
            Storage::disk($disk)->put($thumbnailPath, $thumbnail, $visibility);
        } else {
            $thumbnailPath = null;
        }

        return MediaAsset::create([
            'user_id' => $user->id,
            'collection' => $collection,
            'disk' => $disk,
            'visibility' => $visibility,
            'original_name' => Str::limit($file->getClientOriginalName(), 240, ''),
            'file_name' => $fileName,
            'path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => $mime,
            'extension' => $extension,
            'size' => Storage::disk($disk)->size($path),
            'width' => $width,
            'height' => $height,
            'checksum' => $checksum,
            'status' => 'ready',
            'metadata' => [
                'optimized' => $optimized !== null,
                'thumbnail_generated' => $thumbnailPath !== null,
                'original_size' => $file->getSize(),
                'upload_limit_bytes' => $uploadLimitBytes,
            ],
        ]);
    }

    private function validateImage(UploadedFile $file, int $uploadLimitBytes): void
    {
        if (! $file->isValid()) {
            $this->fail('Dosya yukleme sirasinda hata olustu.');
        }

        if ($file->getSize() <= 0 || $file->getSize() > $uploadLimitBytes) {
            $this->fail('Gorsel en fazla ' . $this->formatBytes($uploadLimitBytes) . ' olabilir.');
        }

        $mime = $file->getMimeType();

        if (! isset(self::IMAGE_MIMES[$mime])) {
            $this->fail('Sadece JPG, PNG, WEBP veya GIF gorseller yuklenebilir.');
        }

        $extension = Str::lower($file->getClientOriginalExtension());

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $this->fail('Dosya uzantisi gorsel formatlariyla uyumlu degil.');
        }

        $imageSize = @getimagesize($file->getRealPath());

        if (! $imageSize || empty($imageSize[0]) || empty($imageSize[1])) {
            $this->fail('Gorsel dogrulanamadi.');
        }

        if (($imageSize[0] * $imageSize[1]) > $this->maxImagePixels()) {
            $this->fail('Gorsel cozunurlugu cok buyuk.');
        }

        $contents = file_get_contents($file->getRealPath(), false, null, 0, 4096) ?: '';

        if (preg_match('/<\?(php|=)|<script\b|MZ\x90|\x7FELF/i', $contents)) {
            $this->fail('Guvenli olmayan dosya icerigi engellendi.');
        }
    }

    private function optimizedImageBytes(
        UploadedFile $file,
        string $mime,
        int $maxDimension,
        int $quality,
        ?string $targetMime = null
    ): ?string {
        if (! extension_loaded('gd') || $mime === 'image/gif') {
            return null;
        }

        $source = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : null,
            default => null,
        };

        if (! $source) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $ratio = min(1, $maxDimension / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $ratio));
        $targetHeight = max(1, (int) round($sourceHeight * $ratio));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        if (($targetMime ?? $mime) !== 'image/jpeg') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
        } else {
            $white = imagecolorallocate($target, 255, 255, 255);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $white);
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        ob_start();
        $outputMime = $targetMime ?? $mime;

        if ($outputMime === 'image/webp' && ! function_exists('imagewebp')) {
            ob_end_clean();
            imagedestroy($source);
            imagedestroy($target);

            return null;
        }

        match ($outputMime) {
            'image/jpeg' => imagejpeg($target, null, $quality),
            'image/png' => imagepng($target, null, 6),
            'image/webp' => imagewebp($target, null, $quality),
            default => imagejpeg($target, null, $quality),
        };
        $bytes = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return $bytes ?: null;
    }

    private function imageUploadLimitBytes(User $user): int
    {
        $limits = config('media.images.limits', []);

        if ($user->isAdmin() || $user->isModerator()) {
            return $this->megabytesToBytes((int) ($limits['moderator_admin_mb'] ?? 50));
        }

        if ((int) $user->forum_reputation >= (int) ($limits['trusted_reputation'] ?? 100)) {
            return $this->megabytesToBytes((int) ($limits['trusted_mb'] ?? 20));
        }

        return $this->megabytesToBytes((int) ($limits['default_mb'] ?? 15));
    }

    private function maxImagePixels(): int
    {
        return max(1, (int) config('media.images.max_pixels', 24_000_000));
    }

    private function megabytesToBytes(int $megabytes): int
    {
        return max(1, $megabytes) * 1024 * 1024;
    }

    private function formatBytes(int $bytes): string
    {
        return (int) ceil($bytes / 1024 / 1024) . ' MB';
    }

    private function fail(string $message): void
    {
        throw ValidationException::withMessages([
            'image' => $message,
        ]);
    }
}
