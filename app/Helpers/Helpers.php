<?php
use App\Helpers\StatusHelper;

if (!function_exists('theme_view')) {
    /**
     * Aktif temanın view yolunu döner.
     * @param string $section Bölüm adı (web, admin, vs.)
     * @param string $view Şablonun yolu (layouts.main, pages.home gibi)
     */
    function theme_view($section, $view)
    {
        $active_theme = config("theme.active_themes.$section");
        $views_path = config("theme.themes.$active_theme.views_path");
        return "$views_path.$view";
    }
}

if (!function_exists('theme_asset')) {
    /**
     * Aktif temanın asset yolunu döner.
     * @param string $section Bölüm adı (web, admin, vs.)
     * @param string $path Dosya yolu (css/style.css, js/app.js gibi)
     */
    function theme_asset($section, $path)
    {
        $active_theme = config("theme.active_themes.$section");
        $assets_path = config("theme.themes.$active_theme.assets_path");
        return asset("$assets_path/$path");
    }
}

if (!function_exists('get_theme_assets_path')) {
    /**
     * Belirtilen temanın asset path yolunu döner.
     * @param string $theme Tema adı
     * @return string|null
     */
    function get_theme_assets_path($theme)
    {
        $themes = config('theme.themes');
        $assetsPath = $themes[$theme]['assets_path'] ?? null;

        return asset($assetsPath);
    }
}


if(!function_exists('status')) {
    function status() {
        return new StatusHelper();
    }
}

if (!function_exists('flashAlert')) {
    /**
     * Laravel session içine flash mesajı ekler.
     *
     * @param string $type success, error, warning, info
     * @param string $message Gösterilecek mesaj
     * @param string $library Kullanılacak alert türü (sweetalert, toastr, bootstrap)
     */
    function flashAlert($type, $message, $library = 'sweetalert')
    {
        session()->flash('alert', [
            'type' => $type,
            'message' => $message,
            'library' => $library
        ]);
    }
}

function ozet($metin, $karakterSayisi = 100) {
    // Metni kelimelere ayır ve ilk kelimeyi al
    $kelimeler = Str::words($metin, 1, '');

    // İlk kelimenin uzunluğunu kontrol et
    if (strlen($kelimeler) > $karakterSayisi) {
        // İlk kelime zaten çok uzunsa, sadece ilk 100 karakteri al
        return Str::substr($kelimeler, 0, $karakterSayisi) . '...';
    }

    // Metnin ilk kelimesini ve geri kalanını birleştir
    $kisaltilmisMetin = $kelimeler . Str::words(Str::after($metin, $kelimeler), 100, '...');

    // Kısaltılmış metnin uzunluğunu kontrol et
    if (strlen($kisaltilmisMetin) > $karakterSayisi + 3) {
        // Kısaltılmış metin hala çok uzunsa, sadece ilk 100 karakteri al
        return Str::substr($kisaltilmisMetin, 0, $karakterSayisi) . '...';
    }

    return $kisaltilmisMetin;
}

function sms_telefon($telefon)
{
    // Boşluk, parantez, tire, + gibi gereksiz karakterleri temizle
    $telefon = preg_replace('/\D/', '', $telefon);

    // Eğer numara zaten 90 ile başlıyorsa ve toplamda 12 hane ise değişiklik yapma
    if (preg_match('/^90\d{10}$/', $telefon)) {
        return $telefon;
    }

    // Eğer numara 0 ile başlıyorsa (örn: 05321112233) baştaki 0'ı kaldır ve 90 ekle
    if (preg_match('/^0(\d{10})$/', $telefon, $matches)) {
        return '90' . $matches[1];
    }

    // Eğer numara 10 haneliyse başına 90 ekle
    if (preg_match('/^\d{10}$/', $telefon)) {
        return '90' . $telefon;
    }

    // Geçersiz numara durumunda null döndür
    return null;
}

if(!function_exists('userAvatar')) {
    function userAvatar(?string $filename, string $type = 'ogrenci'): string
    {
        $path = match ($type) {
            'user', 'admin', 'personel' => config('upload.user.path', 'upload/user'),
            'ogrenci' => config('upload.ogrenci.path', 'upload/ogrenci'),
            default => 'upload/ogrenci',
        };

        $defaultImage = 'user.png';

        // Veri hiç gelmemişse veya boşsa
        if (empty($filename)) {
            return asset("storage/$path/$defaultImage");
        }

        // Dosya varsa göster, yoksa default göster
        if (Storage::disk('public')->exists("$path/$filename")) {
            return asset("storage/$path/$filename");
        }

        return asset("storage/$path/$defaultImage");
    }

}

if (!function_exists('fileicon')) {
    function fileicon(string $filename): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $icons = [
            // Excel
            'excel' => ['xls', 'xlsx', 'csv'],
            // Word
            'word' => ['doc', 'docx'],
            // PDF
            'pdf' => ['pdf'],
            // Image
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
            // Video
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'mkv'],
            // Archive
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
            // Text
            'text' => ['txt', 'md', 'log'],
        ];

        $map = [
            'excel' => 'fa-file-excel',
            'word' => 'fa-file-word',
            'pdf' => 'fa-file-pdf',
            'image' => 'fa-file-image',
            'video' => 'fa-file-video',
            'archive' => 'fa-file-archive',
            'text' => 'fa-file-lines',
            'default' => 'fa-file',
        ];

        foreach ($icons as $type => $extensions) {
            if (in_array($ext, $extensions)) {
                return 'fa-solid ' . $map[$type];
            }
        }

        return 'fa-solid ' . $map['default'];
    }
}
if (!function_exists('logFormat')) {
    function logFormat(int $saniye): string {
        $saat = floor($saniye / 3600);
        $dakika = floor(($saniye % 3600) / 60);
        $saniye = $saniye % 60;

        $parcalar = [];

        if($saat > 0) {
            $parcalar[] = "{$saat}s";
        }

        if($dakika > 0) {
            $parcalar[] = "{$dakika}d";
        }

        if($saniye > 0 || empty($parcalar)) {
            $parcalar[] = "{$saniye}s";
        }

        return implode(' ', $parcalar);
    }
}
if (!function_exists('gecenZaman')) {
    function gecenZaman(int $totalSeconds): string {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}

if (!function_exists('kdv')) {
    /**
     * KDV hesaplama işlemleri
     * Sadece php 8.0+ üstünde çalışır.
     *
     * @param float $tutar
     * @param float $oran
     * @param string $islem: 'ekle', 'cikar', 'hesapla'
     * @return float
     */
    function kdv(float $tutar, float $oran, string $islem = 'ekle'): float
    {
        return match ($islem) {
            'ekle' => $tutar * (1 + ($oran / 100)),
            'cikar' => $tutar / (1 + ($oran / 100)),
            'hesapla' => $tutar * ($oran / 100),
            default => throw new InvalidArgumentException("Geçersiz KDV işlemi türü: $islem"),
        };
    }
}


if(!function_exists('sureHesapla')) {
    /**
     * Saniye cinsinden verilen süreyi saat, dakika ve saniye olarak biçimlendirir
     *
     * @param int $seconds Toplam saniye
     * @return string Biçimlendirilmiş zaman (1 saat 12 dakika 5 saniye)
     */
    function sureHesapla(int $seconds): string {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];

        if($hours > 0) {
            $parts[] = "$hours sa";
        }

        if($minutes > 0) {
            $parts[] = "$minutes dk";
        }

        if($secs > 0 || empty($parts)) {
            $parts[] = "$secs sn";
        }

        return implode(' ', $parts);
    }
}

if(!function_exists('convertSerializeToJson')) {
    /**
     * PHP serialize edilmiş veriyi JSON'a uygun diziye çevirir.
     *
     * - Boş veya boş dizi serialize edilmişse NULL döner
     * - Doluysa array döner
     *
     * @param  string|null  $raw
     * @return array|null
     */
    function convertSerializeToJson(?string $raw): ?array
    {
        if (empty($raw)) {
            return null;
        }

        $tmp = @unserialize($raw);

        if (! is_array($tmp)) {
            return null;
        }

        // Eğer tamamen boş dizi ise -> null
        if (empty($tmp)) {
            return null;
        }

        // Normalize et: sadece string/number, trimle, boşları at
        $arr = array_values(array_filter(
            array_map(fn($v) => is_scalar($v) ? trim((string) $v) : '', $tmp),
            fn($v) => $v !== ''
        ));

        return ! empty($arr) ? $arr : null;
    }


}


