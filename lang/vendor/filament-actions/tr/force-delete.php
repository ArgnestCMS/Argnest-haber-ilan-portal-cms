<?php

return [
    'single' => [
        'label' => 'Kalıcı Sil',
        'modal' => [
            'heading' => ':label Kalıcı Sil',
            'actions' => [
                'delete' => [
                    'label' => 'Kalıcı Sil',
                ],
            ],
        ],
        'notifications' => [
            'deleted' => [
                'title' => 'Kalıcı olarak silindi',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'Seçilenleri Kalıcı Sil',
        'modal' => [
            'heading' => 'Seçilen :label kayıtlarını kalıcı sil',
            'actions' => [
                'delete' => [
                    'label' => 'Kalıcı Sil',
                ],
            ],
        ],
    ],
];
