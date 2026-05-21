<?php

return [
    'label' => 'Profil',
    'form' => [
        'email' => [
            'label' => 'E-Posta',
        ],
        'name' => [
            'label' => 'Ad Soyad',
        ],
        'password' => [
            'label' => 'Yeni şifre',
        ],
        'password_confirmation' => [
            'label' => 'Yeni şifre tekrarı',
        ],
        'current_password' => [
            'label' => 'Mevcut şifre',
            'below_content' => 'Devam etmek için güvenlik amacıyla şifrenizi doğrulayın.',
        ],
        'actions' => [
            'save' => [
                'label' => 'Değişiklikleri Kaydet',
            ],
        ],
    ],
    'actions' => [
        'cancel' => [
            'label' => 'İptal',
        ],
    ],
    'notifications' => [
        'saved' => [
            'title' => 'Kaydedildi',
        ],
    ],
];
