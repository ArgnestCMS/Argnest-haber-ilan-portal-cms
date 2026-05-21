<?php

return [
    'single' => [
        'label' => 'Geri Yükle',
        'modal' => [
            'heading' => ':label Geri Yükle',
            'actions' => [
                'restore' => [
                    'label' => 'Geri Yükle',
                ],
            ],
        ],
        'notifications' => [
            'restored' => [
                'title' => 'Geri yüklendi',
            ],
        ],
    ],
    'multiple' => [
        'label' => 'Seçilenleri Geri Yükle',
        'modal' => [
            'heading' => 'Seçilen :label kayıtlarını geri yükle',
            'actions' => [
                'restore' => [
                    'label' => 'Geri Yükle',
                ],
            ],
        ],
        'notifications' => [
            'restored' => [
                'title' => 'Geri yüklendi',
            ],
        ],
    ],
];
