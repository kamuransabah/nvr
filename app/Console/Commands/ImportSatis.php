<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/*
 * Uygulama Komutu
  php artisan import:satis --offset=0 --limit=10000
 * */

class ImportSatis extends Command
{
    protected $signature = 'import:satis {--offset=0} {--limit=10000}';
    protected $description = 'Canlı sunucudan satış verilerini alır ve yeni yapıya taşır.';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/satis/{$offset}/{$limit}";

        $this->info("Veri çekiliyor: $url");
        $response = Http::withOptions(['verify' => false])->get($url);
        $veriler = $response->json();

        if (!is_array($veriler)) {
            $this->error("Geçersiz veri alındı veya boş.");
            return;
        }

        $sayacYeni = 0;
        $sayacGuncellenen = 0;
        $sayacAtlanan = 0;

        foreach ($veriler as $veri) {
            $siparisNo = 'SP-' . str_pad($veri['id'], 8, '0', STR_PAD_LEFT);

            $mevcut = DB::table('siparisler')->where('siparis_no', $siparisNo)->first();

            $yeni = [
                'siparis_no'       => $siparisNo,
                'user_id'          => $veri['user_id'],
                'personel_id'      => $veri['cm_id'] ?: null,
                'toplam_tutar'     => $veri['fiyat'],
                'indirim_tutari'   => 0,
                'odenecek_tutar'   => $veri['fiyat'],
                'odeme_durum'      => $this->settingsKey('odeme_durum', $veri['odeme_durum']),
                'odeme_turu'       => $this->settingsKey('odeme_turu', $veri['odeme_turu']),
                'satis_kaynak'     => $this->settingsKey('satis_kaynak', $veri['kaynak']),
                'durum'            => 1,
                'ip_adresi'        => $veri['ip_adresi'] ?? null,
                'odeme_tarihi'     => $this->fixOdemeTarihi($veri),
                'personel_notu'    => null,
                'created_at'       => $veri['tarih'],
                'updated_at'       => now(),
            ];

            if ($mevcut) {
                $fark = collect($yeni)->except(['updated_at'])->diffAssoc((array) $mevcut);
                if ($fark->isNotEmpty()) {
                    DB::table('siparisler')->where('id', $mevcut->id)->update($yeni);
                    $sayacGuncellenen++;
                } else {
                    $sayacAtlanan++;
                }
                $siparisId = $mevcut->id;
            } else {
                $siparisId = DB::table('siparisler')->insertGetId($yeni);
                $sayacYeni++;
            }

            // siparis_urunleri
            $urun = [
                'siparis_id'   => $siparisId,
                'satis_turu' => $this->settingsKey('satis_turu', $veri['satis_turu'] ?? 1), // 1 = kurs varsayılan
                'urun_id'      => $veri['kurs_id'],
                'adet'         => 1,
                'birim_fiyat'  => $veri['fiyat'],
                'toplam'       => $veri['fiyat'],
                'created_at'   => $veri['tarih'],
                'updated_at'   => now(),
            ];

            $siparisUrunu = DB::table('siparis_urunleri')
                ->where('siparis_id', $siparisId)
                ->where('urun_id', $urun['urun_id'])
                ->first();

            if (!$siparisUrunu) {
                $urunId = DB::table('siparis_urunleri')->insertGetId($urun);
            } else {
                $urunId = $siparisUrunu->id;
            }

            // egitmen_kazanclari
            if (!empty($veri['egitmen_id']) && $veri['egitmen_id'] != 0) {
                $egitmenVar = DB::table('egitmen_kazanclari')
                    ->where('urun_id', $urunId)
                    ->where('egitmen_id', $veri['egitmen_id'])
                    ->exists();

                if (!$egitmenVar) {
                    DB::table('egitmen_kazanclari')->insert([
                        'urun_id'         => $urunId,
                        'egitmen_id'      => $veri['egitmen_id'],
                        'komisyon_orani'  => null, // anlaşma sabit ücret ise boş kalabilir
                        'komisyon_tutari' => null, // sistem sonrası hesaplanacak
                        'odeme_durum'     => 0,
                        'islenmis'        => $veri['egitmen_hesap'] == 1,
                        'odeme_tarihi'    => null,
                        'created_at'      => $veri['tarih'],
                        'updated_at'      => now(),
                    ]);
                }
            }
        }

        $this->info("Yeni eklenen: $sayacYeni");
        $this->info("Güncellenen: $sayacGuncellenen");
        $this->info("Aynı kalan: $sayacAtlanan");
    }

    private function settingsKey(string $type, $keyOrValue): int
    {
        $row = is_numeric($keyOrValue)
            ? DB::table('settings')->where('type', $type)->where('key', (int)$keyOrValue)->first()
            : DB::table('settings')->where('type', $type)->where('value', $keyOrValue)->first();

        return $row->key ?? 0;
    }

    private function fixOdemeTarihi(array $veri): ?string
    {
        if ($veri['odeme_durum'] != 1) {
            return null;
        }

        $t = $veri['odeme_tarihi'] ?? null;

        if (empty($t) || $t === '0000-00-00 00:00:00') {
            return $veri['tarih'];
        }

        try {
            $yil = Carbon::parse($t)->year;
            return $yil > 2026 ? $veri['tarih'] : $t;
        } catch (\Exception $e) {
            return $veri['tarih'];
        }
    }

}
