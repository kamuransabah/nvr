<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ImageUploadService
{
    public function upload(UploadedFile $file, string $type): array
    {
        $settings = Config::get("upload.{$type}");
        if (!$settings) {
            throw new \Exception("Config dosyasında {$type} için ayar bulunamadı.");
        }

        // 1) Uzantı ve boyut kontrolü
        $allowed = $settings['allowed_file_types'] ?? [];
        $origExt = strtolower($file->getClientOriginalExtension());
        if (!in_array($origExt, $allowed)) {
            throw new \Exception("Bu dosya türüne izin verilmiyor. Sadece: " . implode(', ', $allowed));
        }

        $maxKB = (int) Config::get("upload.max_file_size", 2048);
        if (($file->getSize() / 1024) > $maxKB) {
            throw new \Exception("Dosya boyutu çok büyük. Max: {$maxKB} KB");
        }

        // 2) Nihai format (istersen config'te sabitle: jpg/png/webp/gif)
        $finalFormat = strtolower($settings['force_format'] ?? $origExt);

        // 3) İsim stratejisi (time | hash | original)
        $strategy = $settings['name_strategy'] ?? 'hash'; // varsayılan hash
        $filename = $this->makeFilename($file, $finalFormat, $strategy);

        // 4) Klasörler
        $folder = trim($settings['path'] ?? 'upload/'.$type, '/');
        Storage::disk('public')->makeDirectory($folder);
        Storage::disk('public')->makeDirectory($folder.'/default');
        if (!empty($settings['create_thumb'])) {
            Storage::disk('public')->makeDirectory($folder.'/thumb');
        }

        // 5) Orijinali default/ altına yaz (her koşulda)
        $tmpPath = $file->getPathname(); // getRealPath bazı ortamlarda boş olabilir
        $stream  = fopen($tmpPath, 'r');
        Storage::disk('public')->put($folder.'/default/'.$filename, $stream);
        if (is_resource($stream)) fclose($stream);

        $w  = (int)($settings['width'] ?? 0);
        $h  = (int)($settings['height'] ?? 0);
        $tw = (int)($settings['thumb_width'] ?? 0);
        $th = (int)($settings['thumb_height'] ?? 0);

        // 6) İşlenmiş (ana) ve thumb üretimi
        try {
            // Sadece görselleri işleyelim
            if (in_array($finalFormat, ['jpg','jpeg','png','webp','gif'])) {
                $manager = new ImageManager(new GdDriver());
                $img     = $manager->read($tmpPath);

                if ($w > 0 || $h > 0) {
                    $img = $img->scale($w > 0 ? $w : null, $h > 0 ? $h : null);
                }

                $encoded = $this->encodeByFormat($img, $finalFormat);
                Storage::disk('public')->put($folder.'/'.$filename, (string)$encoded);

                if (!empty($settings['create_thumb'])) {
                    $thumb = $manager->read($tmpPath);
                    if ($tw > 0 || $th > 0) {
                        $thumb = $thumb->scale($tw > 0 ? $tw : null, $th > 0 ? $th : null);
                    }
                    $tencoded = $this->encodeByFormat($thumb, $finalFormat, true);
                    Storage::disk('public')->put($folder.'/thumb/'.$filename, (string)$tencoded);
                }
            } else {
                // Görsel değilse, ana klasöre aynı adla kopyala
                $stream = fopen($tmpPath, 'r');
                Storage::disk('public')->put($folder.'/'.$filename, $stream);
                if (is_resource($stream)) fclose($stream);
            }
        } catch (\Throwable $e) {
            // Görüntü işleme çökerse bile, en azından işlenmemiş dosyayı ana klasöre koyalım
            Log::error('Image process error: '.$e->getMessage());
            $stream = fopen($tmpPath, 'r');
            Storage::disk('public')->put($folder.'/'.$filename, $stream);
            if (is_resource($stream)) fclose($stream);
        }

        return [
            'default' => $filename,               // orijinal (default/) ile aynı isim
            'image'   => $filename,               // ana dosya (path/)
            'thumb'   => !empty($settings['create_thumb']) ? $filename : null,
        ];
    }

    private function makeFilename(UploadedFile $file, string $ext, string $strategy): string
    {
        $ext = ltrim(strtolower($ext), '.');

        if ($strategy === 'time') {
            return time() . '.' . $ext;
        }

        if ($strategy === 'original') {
            $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $base = Str::slug($base) ?: 'image';
            return $base . '.' . $ext;
        }

        // default: hash
        // Laravel'in güvenilir hashName'ini, dizinsiz versiyonda kullan
        $hashed = pathinfo($file->hashName(), PATHINFO_FILENAME);
        return $hashed . '.' . $ext;
    }

    private function encodeByFormat($image, string $format, bool $thumb = false): string
    {
        return match ($format) {
            'jpg', 'jpeg' => $image->toJpeg($thumb ? 80 : 85),
            'png'         => $image->toPng(),
            'webp'        => $image->toWebp($thumb ? 75 : 80),
            'gif'         => $image->toGif(),
            default       => $image->toJpeg($thumb ? 80 : 85),
        };
    }
}
