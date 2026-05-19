<?php

return [
    'images' => [
        'limits' => [
            'default_mb' => env('MEDIA_IMAGE_DEFAULT_LIMIT_MB', 15),
            'trusted_mb' => env('MEDIA_IMAGE_TRUSTED_LIMIT_MB', 20),
            'moderator_admin_mb' => env('MEDIA_IMAGE_MODERATOR_ADMIN_LIMIT_MB', 50),
            'trusted_reputation' => env('MEDIA_IMAGE_TRUSTED_REPUTATION', 100),
        ],

        'max_pixels' => env('MEDIA_IMAGE_MAX_PIXELS', 24_000_000),
    ],
];
