<?php

namespace App\Support;

use App\Models\User;

class ContentAttachmentLimits
{
    public static function maxMegabytes(?User $user = null): int
    {
        $limits = config('media.content_attachments.limits', []);
        $user ??= auth()->user();

        if ($user && ($user->isAdmin() || $user->isModerator())) {
            return (int) ($limits['moderator_admin_mb'] ?? 100);
        }

        if ($user && (int) $user->forum_reputation >= (int) ($limits['trusted_reputation'] ?? 100)) {
            return (int) ($limits['trusted_mb'] ?? 100);
        }

        return (int) ($limits['default_mb'] ?? 50);
    }

    public static function maxKilobytes(?User $user = null): int
    {
        return self::maxMegabytes($user) * 1024;
    }
}
