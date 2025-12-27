<?php

return [

    // Her bölüm için aktif temalar
    'active_themes' => [
        'web'   => 'web',   // Web için aktif tema
        'admin' => 'admin', // Admin için aktif tema
    ],

    // Tüm temalar ve ayarları
    'themes' => [
        'web' => [
            'name' => 'Web Tema',
            'assets_path' => 'web', // public/web
            'views_path'  => 'web', // resources/views/web
        ],
        'admin' => [
            'name' => 'Admin Tema',
            'assets_path' => 'admin', // public/admin
            'views_path'  => 'admin',
        ],
        'ogrenci' => [
            'name' => 'Öğrenci Dashboard',
            'assets_path' => 'ogrenci',
            'views_path'  => 'ogrenci',
        ],
    ],
];
