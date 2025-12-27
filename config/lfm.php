<?php

/*
|--------------------------------------------------------------------------
| Documentation for this config :
|--------------------------------------------------------------------------
| online  => http://unisharp.github.io/laravel-filemanager/config
| offline => vendor/unisharp/laravel-filemanager/docs/config.md
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Routing
    |--------------------------------------------------------------------------
     */

    'use_package_routes'       => true,

    /*
    |--------------------------------------------------------------------------
    | Shared folder / Private folder
    |--------------------------------------------------------------------------
    |
    | If both options are set to false, then shared folder will be activated.
    |
     */

    'allow_private_folder' => false,
    'allow_shared_folder' => true,
    'shared_folder_name' => 'shares',

    // Flexible way to customize client folders accessibility
    // If you want to customize client folders, publish tag="lfm_handler"
    // Then you can rewrite userField function in App\Handler\ConfigHandler class
    // And set 'user_field' to App\Handler\ConfigHandler::class
    // Ex: The private folder of user will be named as the user id.
    'private_folder_name'      => UniSharp\LaravelFilemanager\Handlers\ConfigHandler::class,


    /*
    |--------------------------------------------------------------------------
    | Folder Names
    |--------------------------------------------------------------------------
     */

    'folder_categories' => [
        'file' => [
            'folder_name' => 'upload/files', // 📂 Dosyalar buraya gidecek
            'startup_view' => 'list',
            'max_size' => 50000, // KB cinsinden
            'thumb' => true,
            'thumb_width' => 80,
            'thumb_height' => 80,
            'valid_mime' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'application/pdf',
                'text/plain',
            ],
        ],
        'image' => [
            'folder_name' => 'upload/images', // 📂 Resimler buraya yüklenecek
            'startup_view' => 'grid',
            'max_size' => 50000, // KB cinsinden
            'thumb' => true,
            'thumb_width' => 200,
            'thumb_height' => 200,
            'valid_mime' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/webp',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
     */

    'paginator' => [
        'perPage' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload / Validation
    |--------------------------------------------------------------------------
     */

    /*
    |--------------------------------------------------------------------------
    | Storage Disk Ayarları
    |--------------------------------------------------------------------------
    */
    'disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Dosyaların Kaydedileceği Ana Dizin
    |--------------------------------------------------------------------------
    |
    | Eğer base_directory yanlış ayarlanmışsa, LFM hatalı bir yol oluşturabilir.
    |
    */
    'base_directory' => 'upload',

    'rename_file' => true, // Dosyalar benzersiz isimlerle kaydedilsin mi?
    'rename_duplicates' => true, // Aynı isimde dosya varsa "_1" gibi yeni isimlendirilsin mi?
    'alphanumeric_filename' => false, // Sadece harf ve rakamlardan oluşan dosya isimleri kullanılsın mı?
    'alphanumeric_directory' => false,

    'should_validate_size' => true,
    'should_validate_mime' => true,

    'over_write_on_duplicate' => false, // Aynı isimde dosya varsa üzerine yazılsın mı?

    'disallowed_mimetypes' => ['text/x-php', 'text/html', 'text/plain'], // PHP veya HTML dosyaları yüklenmesin
    'disallowed_extensions' => ['php', 'html'],

    // Item Columns
    'item_columns' => ['name', 'url', 'time', 'icon', 'is_file', 'is_image', 'thumb_url'],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail
    |--------------------------------------------------------------------------
     */

    'should_create_thumbnails' => false,
    'thumb_folder_name' => 'thumbs',
    'raster_mimetypes' => ['image/jpeg', 'image/pjpeg', 'image/png'],
    'thumb_img_width' => 400,
    'thumb_img_height' => 400,

    /*
    |--------------------------------------------------------------------------
    | File Extension Information
    |--------------------------------------------------------------------------
     */

    'file_type_array'          => [
        'pdf'  => 'Adobe Acrobat',
        'doc'  => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls'  => 'Microsoft Excel',
        'xlsx' => 'Microsoft Excel',
        'zip'  => 'Archive',
        'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
        'ppt'  => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
    ],

    /*
    |--------------------------------------------------------------------------
    | php.ini override
    |--------------------------------------------------------------------------
    |
    | These values override your php.ini settings before uploading files
    | Set these to false to ingnore and apply your php.ini settings
    |
    | Please note that the 'upload_max_filesize' & 'post_max_size'
    | directives are not supported.
     */
    'php_ini_overrides'        => [
        'memory_limit' => '256M',
    ],
];
