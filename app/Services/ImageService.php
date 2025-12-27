<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageService
{
    /**
     * Resmi yükler ve verilen dizine kaydeder.
     */
    public function upload($file, string $type): ?string
    {
        $imageUploadService = app(ImageUploadService::class);
        $imagePaths = $imageUploadService->upload($file, $type);
        return $imagePaths['image'] ?? null;
    }

    /**
     * Belirtilen dosyaları siler (Varsa thumbnail'ı da siler)
     */
    public function delete($fileName, string $type): void
    {
        $config = Config::get("upload.{$type}");

        if (!$config) {
            throw new \Exception("Config dosyasında '{$type}' için ayar bulunamadı.");
        }

        // Dosya yollarını belirle
        $defaultPath = "{$config['path']}/default/{$fileName}";
        $mainPath = "{$config['path']}/{$fileName}";
        $thumbPath = "{$config['path']}/thumb/{$fileName}";

        // Varsayılan resim siliniyor
        if (Storage::disk('public')->exists($defaultPath)) {
            Storage::disk('public')->delete($defaultPath);
        }

        // Ana resim siliniyor
        if (Storage::disk('public')->exists($mainPath)) {
            Storage::disk('public')->delete($mainPath);
        }

        // Eğer thumb oluşturulmuşsa ve varsa thumb versiyonunu da sil
        if ($config['create_thumb'] && Storage::disk('public')->exists($thumbPath)) {
            Storage::disk('public')->delete($thumbPath);
        }
    }

    /**
     * Resmi günceller (Eskiyi siler, yenisini ekler)
     */
    public function update($file, ?string $oldFileName, string $type): ?string
    {
        if ($oldFileName) {
            $this->delete($oldFileName, $type); // Önce eskiyi sil
        }
        return $this->upload($file, $type); // Yeni resmi yükle
    }
}
