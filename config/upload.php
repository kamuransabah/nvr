<?php

return [
    'default_disk' => 'public', // `storage/app/public`

    'max_file_size' => 20480, // KB cinsinden max dosya boyutu (20MB)

    'blog' => [
        'path' => 'upload/blog',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'width' => 1200,
        'height' => null,
        'thumb_width' => 400,
        'thumb_height' => null,
        'create_thumb' => true,
    ],

    'kurs' => [
        'path' => 'upload/kurs',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'width' => 1200,
        'height' => null,
        'thumb_width' => 400,
        'thumb_height' => null,
        'create_thumb' => true,
    ],

    'sayfa' => [
        'path' => 'upload/sayfa',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'width' => 1200,
        'height' => null,
        'thumb_width' => 400,
        'thumb_height' => null,
        'create_thumb' => false,
    ],

    'user' => [
        'path' => 'upload/user',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'width' => 1200,
        'height' => 800,
        'thumb_width' => 400,
        'thumb_height' => 300,
        'create_thumb' => true,
    ],

    'ogrenci' => [
        'path' => 'upload/ogrenci',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'width' => 1200,
        'height' => 800,
        'thumb_width' => 400,
        'thumb_height' => 300,
        'create_thumb' => true,
    ],

    'urun' => [
        'path' => 'upload/urun',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'width' => 1200,
        'height' => 800,
        'thumb_width' => 400,
        'thumb_height' => 300,
        'create_thumb' => true,
    ],

    'belge' => [
        'path' => 'upload/belge',
        'allowed_file_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf','docx', 'doc', 'xlsx', 'xls', 'pptx', 'ppt', 'txt', 'zip', 'rar', '7z'],
        'create_thumb' => false,
        'width' => null,
        'height' => null,
    ],

    'sertifika' => [
        'path' => 'upload/sertifika',
        'allowed_file_types' => ['pdf'],
        'create_thumb' => false,
        'width' => null,
        'height' => null,
    ],
];
