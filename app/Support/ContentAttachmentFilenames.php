<?php

namespace App\Support;

use App\Models\MediaAsset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentAttachmentFilenames
{
    private const EXTENSIONS_BY_MIME = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
        'image/gif' => ['gif'],
        'application/pdf' => ['pdf'],
    ];

    private const CANONICAL_EXTENSION_BY_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
    ];

    /**
     * Filament can ask for the same temporary upload's storage name more than once.
     *
     * @var array<string, string>
     */
    private static array $generatedFilenames = [];

    public static function forUploadedFile(
        UploadedFile $file,
        string $directory,
        ?string $ignorePath = null,
        string $errorKey = 'content_attachments',
    ): string {
        $cacheKey = self::cacheKey($file, $directory, $ignorePath);

        if (isset(self::$generatedFilenames[$cacheKey])) {
            return self::$generatedFilenames[$cacheKey];
        }

        $mimeType = $file->getMimeType() ?: 'application/octet-stream';
        $extension = self::safeExtension($mimeType, $file->getClientOriginalExtension());
        $baseName = self::safeBaseName(pathinfo(self::clientFilename($file), PATHINFO_FILENAME));
        $filename = $baseName . '.' . $extension;
        $path = trim($directory, '/') . '/' . $filename;
        $counter = 1;

        while (self::hasActiveStoredCollision($path, $ignorePath)) {
            $filename = $baseName . '-' . $counter . '.' . $extension;
            $path = trim($directory, '/') . '/' . $filename;
            $counter++;
        }

        return self::$generatedFilenames[$cacheKey] = $filename;
    }

    private static function hasActiveStoredCollision(string $path, ?string $ignorePath): bool
    {
        if ($path === $ignorePath) {
            return false;
        }

        if (! Storage::disk('public')->exists($path)) {
            return false;
        }

        return MediaAsset::query()
            ->where('disk', 'public')
            ->where('path', $path)
            ->exists();
    }

    private static function clientFilename(UploadedFile $file): string
    {
        $filename = str_replace(["\0", '\\'], ['', '/'], $file->getClientOriginalName());

        return basename($filename);
    }

    private static function safeBaseName(string $name): string
    {
        $name = preg_replace('/[\x00-\x1F\x7F]+/u', '', $name) ?: '';
        $name = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $name);
        $name = str_replace('..', '.', $name);
        $name = preg_replace('/\s+/u', ' ', $name) ?: '';
        $name = trim($name, " .\t\n\r\0\x0B");

        return Str::limit($name ?: 'dosya', 120, '');
    }

    private static function cacheKey(UploadedFile $file, string $directory, ?string $ignorePath): string
    {
        return implode('|', [
            trim($directory, '/'),
            $file->getRealPath() ?: spl_object_id($file),
            $file->getClientOriginalName(),
            $ignorePath ?? '',
        ]);
    }

    private static function safeExtension(string $mimeType, string $clientExtension): string
    {
        $clientExtension = Str::lower(trim($clientExtension));
        $allowedExtensions = self::EXTENSIONS_BY_MIME[$mimeType] ?? [];

        if (in_array($clientExtension, $allowedExtensions, true)) {
            return $clientExtension;
        }

        return self::CANONICAL_EXTENSION_BY_MIME[$mimeType] ?? 'bin';
    }
}
