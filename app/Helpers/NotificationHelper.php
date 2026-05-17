<?php

namespace App\Helpers;

use App\Jobs\SendNotificationJob;
use App\Models\User;

class NotificationHelper
{
    public static function sendToUser(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        array $data = []
    ): void {
        SendNotificationJob::dispatch(
            users: [$userId],
            type: $type,
            title: $title,
            message: $message,
            url: $url,
            data: $data
        );
    }

    public static function sendToRoles(
        array $roles,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        array $data = []
    ): void {
        $users = User::query()
            ->whereIn('role', $roles)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        if (empty($users)) {
            return;
        }

        SendNotificationJob::dispatch(
            users: $users,
            type: $type,
            title: $title,
            message: $message,
            url: $url,
            data: $data
        );
    }

    public static function sendToAdmins(
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        array $data = []
    ): void {
        self::sendToRoles(
            roles: ['admin'],
            type: $type,
            title: $title,
            message: $message,
            url: $url,
            data: $data
        );
    }

    public static function sendToModerators(
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        array $data = []
    ): void {
        self::sendToRoles(
            roles: ['admin', 'moderator'],
            type: $type,
            title: $title,
            message: $message,
            url: $url,
            data: $data
        );
    }
}