<?php

return [
    'builder' => [
        'actions' => [
            'delete' => [
                'label' => 'Sil',
            ],
            'edit' => [
                'label' => 'Düzenle',
                'modal' => [
                    'actions' => [
                        'save' => [
                            'label' => 'Değişiklikleri Kaydet',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'file_upload' => [
        'editor' => [
            'actions' => [
                'cancel' => [
                    'label' => 'İptal',
                ],
                'reset' => [
                    'label' => 'Sıfırla',
                ],
                'save' => [
                    'label' => 'Kaydet',
                ],
            ],
        ],
    ],
    'markdown_editor' => [
        'tools' => [
            'attach_files' => 'Dosya ekle',
        ],
    ],
    'rich_editor' => [
        'tools' => [
            'attach_files' => 'Dosya ekle',
        ],
    ],
    'select' => [
        'actions' => [
            'create_option' => [
                'label' => 'Oluştur',
                'modal' => [
                    'heading' => 'Oluştur',
                    'actions' => [
                        'create' => [
                            'label' => 'Oluştur',
                        ],
                        'create_another' => [
                            'label' => 'Oluştur ve yeni ekle',
                        ],
                    ],
                ],
            ],
        ],
        'no_search_results_message' => 'Aramanızla eşleşen seçenek yok.',
        'placeholder' => 'Bir seçenek seçin',
        'searching_message' => 'Aranıyor...',
        'search_prompt' => 'Aramak için yazmaya başlayın...',
    ],
    'tags_input' => [
        'actions' => [
            'delete' => [
                'label' => 'Sil',
            ],
        ],
        'placeholder' => 'Yeni etiket',
    ],
];
