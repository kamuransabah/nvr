<?php
namespace App\Helpers;

class StatusHelper
{
    /**
     * Farklı veri türlerine göre dinamik metinler ve class döndürür.
     *
     * @param int|string $status Status ID veya key
     * @param string $type Hangi bilgiyi almak istediğimiz: "class" | "text"
     * @param string|null $context İçerik türü: "blog" | "product" | null
     * @return string
     */
    public static function get($status, string $type = 'class', ?string $context = null): string
    {
        // Genel durum listesi (status ID => [class, default text])
        $statuses = [
            0 => ['class' => 'secondary', 'text' => 'Pasif'],
            1 => ['class' => 'primary', 'text' => 'Aktif'],
            2 => ['class' => 'warning', 'text' => 'Bekliyor'],
            3 => ['class' => 'danger', 'text' => 'Silinmiş'],
            4 => ['class' => 'success', 'text' => 'Yeni'],
            5 => ['class' => 'info', 'text' => 'Bilgi'],
            6 => ['class' => 'dark', 'text' => 'Bilinmiyor'],
        ];

        // Özel class tanımlamaları (veri türüne göre farklı classlar)
        $customClasses = [
            'data' => [
                0 => 'dark',
                1 => 'primary',
                2 => 'primary',
                3 => 'danger',
                4 => 'warning',
                5 => 'success',
                6 => 'info',
            ],
            'user' => [
                0 => 'dark',
                1 => 'success',
                2 => 'danger',
                3 => 'warning'
            ],
            'ogrenci_kaynak' => [
                0 => 'dark',
                1 => 'info',
                2 => 'primary',
                3 => 'success',
                4 => 'warning',
            ],
            'ogrenci_sozlesme' => [
                0 => 'danger',
                1 => 'success',
            ],
            'odeme_durum' => [
                0 => 'secondary',
                1 => 'success',
                2 => 'warning',
                3 => 'danger',
            ],
            'belge_durum' => [
                0 => 'secondary',
                1 => 'warning',
                2 => 'success',
                3 => 'danger',
            ],
            'sinav_turu' => [
                1 => 'success',
                2 => 'primary',
                3 => 'info',
            ],
            'kargo_turu' => [
                0 => 'success',
                1 => 'primary',
            ],
            'yetki' => [
                'admin' => 'primary',
                'personel' => 'info',
                'superadmin' => 'danger',
                'egitmen' => 'success',
                'kurum' => 'warning',
            ],
            // Diğer bölümler için özel class tanımlamaları buraya eklenebilir
        ];

        // Özel metin tanımlamaları (veri türüne göre farklı metinler)
        $customTexts = [
            'blog' => [
                0 => 'Pasif',
                1 => 'Yayında',
            ],
            'ogrenci_sozlesme' => [
                0 => 'Onaylamadı',
                1 => 'Onayladı'
            ],
            'product' => [
                0 => 'Satışa Kapalı',
                1 => 'Satışta',
            ],
            'kargo_turu' => [
                0 => 'Online Satış',
                1 => 'Fiziksel',
            ],
            'kitap_destegi' => [
                0 => 'Hayır',
                1 => 'Evet'
            ]
            // Diğer bölümler için özel metin tanımlamaları buraya eklenebilir
        ];

        $text = $statuses[$status]['text'] ?? '--';
        $class = $statuses[$status]['class'] ?? 'secondary';

        // Eğer özel bir veri türü varsa, ona göre class ata
        if ($context && isset($customClasses[$context][$status])) {
            $class = $customClasses[$context][$status];
        }

        // Eğer özel bir veri türü varsa, ona göre metin ata
        if ($context && isset($customTexts[$context][$status])) {
            $text = $customTexts[$context][$status];
        }

        // Kullanıcının istediği bilgiye göre dönüş yap
        return $type === 'text' ? $text : $class;
    }
}
