<?php

return [
    'single' => [
        'label' => 'Sil',
        'modal' => [
            'heading' => ':label Sil',
            'actions' => [
                'delete' => [
                    'label' => 'Sil',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Silindi',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'Seçilenleri Sil',
        'modal' => [
            'heading' => 'Seçilen :label kayıtlarını sil',
            'actions' => [
                'delete' => [
                    'label' => 'Sil',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Silindi',
            ],
        ],
    ],
];
