<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Doğrulama Mesajları (TR)
    |--------------------------------------------------------------------------
    |
    | Bu dosyada Laravel'in varsayılan doğrulama mesajlarının Türkçe
    | çevirileri bulunur. Eksik kalan bir anahtar olursa framework
    | "validation.max.numeric" gibi ham bir anahtarı gösterebilir.
    | O yüzden bu dosyada numeric/file/string/array varyantları da
    | dahil tüm sık kullanılan kurallar yer alır.
    |
    */

    'accepted'             => ':attribute kabul edilmelidir.',
    'accepted_if'          => ':other :value olduğunda :attribute kabul edilmelidir.',
    'active_url'           => ':attribute geçerli bir URL olmalıdır.',
    'after'                => ':attribute :date tarihinden sonra olmalıdır.',
    'after_or_equal'       => ':attribute :date tarihinden sonra veya eşit olmalıdır.',
    'alpha'                => ':attribute yalnızca harflerden oluşmalıdır.',
    'alpha_dash'           => ':attribute yalnızca harf, rakam, tire ve alt çizgi içerebilir.',
    'alpha_num'            => ':attribute yalnızca harf ve rakamlardan oluşmalıdır.',
    'array'                => ':attribute bir dizi (array) olmalıdır.',
    'before'               => ':attribute :date tarihinden önce olmalıdır.',
    'before_or_equal'      => ':attribute :date tarihinden önce veya eşit olmalıdır.',
    'between'              => [
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'file'    => ':attribute :min ile :max kilobayt arasında olmalıdır.',
        'string'  => ':attribute :min ile :max karakter arasında olmalıdır.',
        'array'   => ':attribute :min ile :max öğe arasında sahip olmalıdır.',
    ],
    'boolean'              => ':attribute alanı doğru ya da yanlış olmalıdır.',
    'confirmed'            => ':attribute doğrulaması eşleşmiyor.',
    'current_password'     => 'Şifre hatalı.',
    'date'                 => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals'          => ':attribute :date ile aynı tarih olmalıdır.',
    'date_format'          => ':attribute :format biçimi ile eşleşmiyor.',
    'declined'             => ':attribute reddedilmelidir.',
    'declined_if'          => ':other :value olduğunda :attribute reddedilmelidir.',
    'different'            => ':attribute ile :other farklı olmalıdır.',
    'digits'               => ':attribute :digits basamaklı olmalıdır.',
    'digits_between'       => ':attribute :min ile :max basamak arasında olmalıdır.',
    'dimensions'           => ':attribute geçersiz görsel boyutlarına sahip.',
    'distinct'             => ':attribute alanı yinelemeli (tekrarlı) değere sahip.',
    'doesnt_end_with'      => ':attribute şu değerlerden biriyle bitemez: :values.',
    'doesnt_start_with'    => ':attribute şu değerlerden biriyle başlayamaz: :values.',
    'email'                => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'ends_with'            => ':attribute şu değerlerden biriyle bitmelidir: :values.',
    'enum'                 => 'Seçilen :attribute geçersiz.',
    'exists'               => 'Seçtiğiniz :attribute geçersizdir.',
    'file'                 => ':attribute bir dosya olmalıdır.',
    'filled'               => ':attribute alanı doldurulmalıdır.',
    'gt'                   => [
        'numeric' => ':attribute :value değerinden büyük olmalıdır.',
        'file'    => ':attribute :value kilobayttan büyük olmalıdır.',
        'string'  => ':attribute :value karakterden uzun olmalıdır.',
        'array'   => ':attribute :value öğeden fazla olmalıdır.',
    ],
    'gte'                  => [
        'numeric' => ':attribute :value değerinden büyük ya da eşit olmalıdır.',
        'file'    => ':attribute :value kilobayttan büyük ya da eşit olmalıdır.',
        'string'  => ':attribute :value karakter ya da daha uzun olmalıdır.',
        'array'   => ':attribute en az :value öğeye sahip olmalıdır.',
    ],
    'image'                => ':attribute bir resim dosyası olmalıdır.',
    'in'                   => ':attribute için geçersiz bir değer seçtiniz.',
    'in_array'             => ':attribute :other içinde bulunmuyor.',
    'integer'              => ':attribute alanı sadece rakamlardan oluşabilir.',
    'ip'                   => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4'                 => ':attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'                 => ':attribute geçerli bir IPv6 adresi olmalıdır.',
    'json'                 => ':attribute geçerli bir JSON olmalıdır.',
    'lowercase'            => ':attribute küçük harf olmalıdır.',
    'lt'                   => [
        'numeric' => ':attribute :value değerinden küçük olmalıdır.',
        'file'    => ':attribute :value kilobayttan küçük olmalıdır.',
        'string'  => ':attribute :value karakterden kısa olmalıdır.',
        'array'   => ':attribute :value öğeden az olmalıdır.',
    ],
    'lte'                  => [
        'numeric' => ':attribute :value değerinden küçük ya da eşit olmalıdır.',
        'file'    => ':attribute :value kilobayttan küçük ya da eşit olmalıdır.',
        'string'  => ':attribute :value karakter ya da daha kısa olmalıdır.',
        'array'   => ':attribute en fazla :value öğe içerebilir.',
    ],
    'mac_address'          => ':attribute geçerli bir MAC adresi olmalıdır.',
    'max'                  => [
        'numeric' => ':attribute en fazla :max olabilir.',
        'file'    => ':attribute en fazla :max kilobayt olabilir.',
        'string'  => ':attribute en fazla :max karakter olabilir.',
        'array'   => ':attribute en fazla :max öğe içerebilir.',
    ],
    'mimes'                => ':attribute dosyası şu formatlarda olmalıdır: :values.',
    'mimetypes'            => ':attribute dosyası şu formatlarda olmalıdır: :values.',
    'min'                  => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'file'    => ':attribute en az :min kilobayt olmalıdır.',
        'string'  => ':attribute en az :min karakter olmalıdır.',
        'array'   => ':attribute en az :min öğe içermelidir.',
    ],
    'multiple_of'          => ':attribute :value değerinin katı olmalıdır.',
    'not_in'               => 'Seçilen :attribute geçersiz.',
    'not_regex'            => ':attribute biçimi geçersiz.',
    'numeric'              => ':attribute bir sayı olmalıdır.',
    'password'             => [
        'letters'       => ':attribute en az bir harf içermelidir.',
        'mixed'         => ':attribute en az bir büyük ve bir küçük harf içermelidir.',
        'numbers'       => ':attribute en az bir rakam içermelidir.',
        'symbols'       => ':attribute en az bir sembol içermelidir.',
        'uncompromised' => 'Verilen :attribute bir veri ihlalinde yer almış. Lütfen farklı bir şifre kullanın.',
    ],
    'present'              => ':attribute alanı mevcut olmalıdır.',
    'prohibited'           => ':attribute alanı yasaktır.',
    'prohibited_if'        => ':other :value olduğunda :attribute alanı yasaktır.',
    'prohibited_unless'    => ':other :values içinde olmadıkça :attribute alanı yasaktır.',
    'prohibits'            => ':attribute alanı :other alanının bulunmasını engeller.',
    'regex'                => ':attribute biçimi geçersiz.',
    'required'             => ':attribute alanı zorunludur.',
    'required_array_keys'  => ':attribute alanı şu anahtarları içermelidir: :values.',
    'required_if'          => ':other :value olduğunda :attribute alanı zorunludur.',
    'required_unless'      => ':other :values içinde olmadıkça :attribute alanı zorunludur.',
    'required_with'        => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_with_all'    => ':values mevcut olduğunda :attribute alanı zorunludur.',
    'required_without'     => ':values mevcut değilken :attribute alanı zorunludur.',
    'required_without_all' => ':values değerlerinin hiçbiri mevcut değilken :attribute alanı zorunludur.',
    'same'                 => ':attribute ile :other eşleşmelidir.',
    'size'                 => [
        'numeric' => ':attribute :size olmalıdır.',
        'file'    => ':attribute :size kilobayt olmalıdır.',
        'string'  => ':attribute :size karakter olmalıdır.',
        'array'   => ':attribute :size öğe içermelidir.',
    ],
    'starts_with'          => ':attribute şu değerlerden biriyle başlamalıdır: :values.',
    'string'               => ':attribute bir metin olmalıdır.',
    'timezone'             => ':attribute geçerli bir zaman dilimi olmalıdır.',
    'unique'               => 'Bu :attribute zaten kullanılıyor.',
    'uploaded'             => ':attribute yüklenemedi.',
    'uppercase'            => ':attribute büyük harf olmalıdır.',
    'url'                  => ':attribute geçerli bir URL olmalıdır.',
    'uuid'                 => ':attribute geçerli bir UUID olmalıdır.',

    /*
    |--------------------------------------------------------------------------
    | Özel Mesajlar (custom)
    |--------------------------------------------------------------------------
    | "attribute.rule" formatında anahtarlar vererek belirli alanlar için
    | özelleştirilmiş mesajlar tanımlayabilirsiniz.
    */

    'custom' => [
        // 'kategori_id.integer' => 'Kategori alanı sadece rakamlardan oluşabilir.',
        // 'belgeler.array'      => 'Belgeler formatı geçersiz.',
        // 'ozellikler.array'    => 'Özellikler formatı geçersiz.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute İsimleri
    |--------------------------------------------------------------------------
    | Bu bölümdeki anahtarlar form alan isimlerini daha okunur hâle getirir.
    | Örn: "kategori_id" yerine "Kategori" görünür.
    */

    'attributes' => [
        'kategori_id'         => 'Kategori',
        'kurs_adi'            => 'Kurs Adı',
        'permalink'           => 'Permalink',
        'ozet'                => 'Özet',
        'aciklama'            => 'Açıklama',

        'belgeler'            => 'Gerekli Belgeler',
        'ozellikler'          => 'Özellikler',
        'ozellikler.*.ozellik'=> 'Özellik',
        'neler_ogrenecegim'         => 'Neler Öğreneceğim',
        'neler_ogrenecegim.*.metin' => 'Neler Öğreneceğim öğesi',

        'gereksinimler'       => 'Gereksinimler',
        'kurs_icerigi'        => 'Kurs İçeriği',

        'gecme_notu'          => 'Geçme Notu',
        'kurs_puani'          => 'Kurs Puanı',
        'label'               => 'Etiket',
        'fiyat'               => 'Fiyat',
        'kdv_orani'           => 'KDV Oranı',
        'ucretsiz'            => 'Ücretsiz',
        'egitim_suresi'       => 'Eğitim Süresi',
        'egitim_sureci'       => 'Eğitim Süreci',
        'sertifika_turu'      => 'Sertifika Türü',
        'kitap_destegi'       => 'Kitap Desteği',
        'sinav_basari_orani'  => 'Sınav Başarı Oranı',
        'ders_sayisi'         => 'Ders Sayısı',
        'egitim_seviyesi'     => 'Eğitim Seviyesi',

        'resim'               => 'Resim',
        'sertifika_ornegi'    => 'Sertifika Örneği',

        'seo_title'           => 'SEO Başlığı',
        'seo_description'     => 'SEO Açıklaması',
        'sira'                => 'Sıra',
        'tur'                 => 'Tür',
        'durum'               => 'Durum',
    ],

    /*
    |--------------------------------------------------------------------------
    | Değer Haritaları (values)
    |--------------------------------------------------------------------------
    | Bazı alanların belirli değerleri için okunur karşılıklar:
    */

    'values' => [
        'ucretsiz' => [
            'E' => 'Evet',
            'H' => 'Hayır',
        ],
    ],

];
