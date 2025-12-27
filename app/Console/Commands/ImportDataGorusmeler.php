<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;

/*
 * uygulama komutu php artisan import:datagorusmeler --offset=0 --limit=10000
 * son olarak bunu uyguladım (eksik veriler için tekrar uygulanabilir.)
 * php artisan import:datagorusmeler --offset=750000 --limit=10000
 *
 * 550.000 den geri geliyordum,
 * yaklaşık 1M kayıt var, eksi kayıtlar arşivlenip taşınacak.
 * */

class ImportDataGorusmeler extends Command
{
    protected $signature = 'import:datagorusmeler {--offset=0} {--limit=10000}';
    protected $description = 'Import data_gorusmeler from CI3';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit  = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_data_gorusme/{$offset}/{$limit}";
        $this->info("Fetching $url ...");

        $resp = Http::withOptions(['verify' => false])->get($url);
        if ($resp->failed()) {
            $this->error("HTTP error: ".$resp->status());
            return 1;
        }

        $rows = $resp->json();

        // JSON değilse veya boşsa
        if (!is_array($rows) || empty($rows)) {
            $this->warn("Empty or invalid JSON. First 200 chars of body:");
            $this->line(substr($resp->body(), 0, 200)); // HTML hata sayfası yakalamak için
            return 0;
        }

        $this->info("Received ".count($rows)." rows");

        // === PRE-FLIGHT: FK var mı? ===
        $uniqDataIds     = array_values(array_unique(array_filter(array_column($rows, 'data_id'))));
        $uniqUserIds     = array_values(array_unique(array_filter(array_column($rows, 'cm_id'))));
        $uniqKursIds     = array_values(array_unique(array_filter(array_column($rows, 'kurs_id'))));
        $uniqOlumsuzIds  = array_values(array_unique(array_filter(array_column($rows, 'red_id'))));
        $uniqRandevuIds  = array_values(array_unique(array_filter(array_column($rows, 'randevu_id'))));

        $foundData    = $uniqDataIds ? DB::table('data')->whereIn('id', array_slice($uniqDataIds, 0, 10000))->count() : 0;
        $foundUsers   = $uniqUserIds ? DB::table('users')->whereIn('id', array_slice($uniqUserIds, 0, 10000))->count() : 0;
        $foundKurs    = $uniqKursIds ? DB::table('kurslar')->whereIn('id', array_slice($uniqKursIds, 0, 10000))->count() : 0;
        $foundOlum    = $uniqOlumsuzIds ? DB::table('data_olumsuz_nedenler')->whereIn('id', array_slice($uniqOlumsuzIds, 0, 10000))->count() : 0;
        $foundRand    = $uniqRandevuIds ? DB::table('data_randevular')->whereIn('id', array_slice($uniqRandevuIds, 0, 10000))->count() : 0;

        $this->info("Preflight -> data: ".count($uniqDataIds)."/$foundData, users: ".count($uniqUserIds)."/$foundUsers, kurs: ".count($uniqKursIds)."/$foundKurs, olumsuz: ".count($uniqOlumsuzIds)."/$foundOlum, randevu: ".count($uniqRandevuIds)."/$foundRand");

        if ($foundData === 0 && count($uniqDataIds) > 0) {
            $this->error("Local 'data' tablosunda hiç eşleşen ID yok. Önce lead (data) importu veya ID mapping gerekiyor.");
            return 1;
        }

        // Cache yardımcıları
        $cache = [
            'data'    => [],
            'users'   => [],
            'kurs'    => [],
            'olumsuz' => [],
            'randevu' => [],
        ];
        $exists = function (string $tbl, $id) use (&$cache) {
            if (!$id) return false;
            if (array_key_exists($id, $cache[$tbl])) return $cache[$tbl][$id];
            $found = DB::table($tbl === 'kurs' ? 'kurslar'
                : ($tbl === 'olumsuz' ? 'data_olumsuz_nedenler'
                    : ($tbl === 'randevu' ? 'data_randevular' : $tbl)))
                ->where('id', $id)->exists();
            return $cache[$tbl][$id] = $found;
        };

        $parseDate = function ($val) {
            if (!$val) return null;
            try { return Carbon::parse($val); } catch (\Throwable $e) { return null; }
        };

        // Sayaçlar
        $ins = $upd = $skipDataFk = $skipDate = $qErr = 0;

        foreach ($rows as $i => $row) {
            // --- Kaynak alanlar ---
            $id           = $row['id']        ?? null;
            $dataId       = $row['data_id']   ?? null;     // ZORUNLU FK
            $kursId       = $row['kurs_id']   ?? null;     // opsiyonel
            $personelId   = $row['cm_id']     ?? null;     // opsiyonel
            $randevuId    = $row['randevu_id']?? null;     // opsiyonel
            $olumsuzId    = $row['red_id']    ?? null;     // opsiyonel

            // TINYINT(1) için 'kayit' cast; metin gelirse NULL yapıyorum
            $kayitRaw     = $row['kayit']     ?? null;
            $kayit        = is_null($kayitRaw) ? null : (is_numeric($kayitRaw) ? (int)$kayitRaw : null);

            $personelNotu = $row['not']       ?? null;
            $createdRaw   = $row['tarih']     ?? null;

            // --- FK doğrulamaları ---
            if (!$exists('data', $dataId)) { // data_id yoksa KAYDI ATLIYORUZ
                $skipDataFk++;
                if ($skipDataFk <= 5) $this->warn("Skip: data_id={$dataId} not found (id={$id})");
                continue;
            }
            if (!$exists('users', $personelId))  $personelId = null;
            if (!$exists('kurs',  $kursId))      $kursId = null;
            if (!$exists('olumsuz',$olumsuzId))  $olumsuzId = null;
            if (!$exists('randevu',$randevuId))  $randevuId = null;

            // created_at parse
            $createdAt = $parseDate($createdRaw) ?: null;

            $payload = [
                'id'            => $id,
                'data_id'       => $dataId,
                'kurs_id'       => $kursId,
                'personel_id'   => $personelId,
                'randevu_id'    => $randevuId,
                'olumsuz_id'    => $olumsuzId,
                'kayit'         => $kayit,
                'personel_notu' => $personelNotu,
                'created_at'    => $createdAt ? $createdAt->format('Y-m-d H:i:s') : null,
                'updated_at'    => now()->format('Y-m-d H:i:s'),
            ];

            try {
                // upsert: varsa günceller, yoksa ekler
                $aff = DB::table('data_gorusmeler')->upsert(
                    $payload,
                    ['id'],
                    ['data_id','kurs_id','personel_id','randevu_id','olumsuz_id','kayit','personel_notu','created_at','updated_at']
                );
                if ($aff === 1) $ins++;
                elseif ($aff === 2) $upd++;
            } catch (QueryException $e) {
                $qErr++;
                if ($qErr <= 5) $this->error("QueryException (id={$id}): ".$e->getMessage());
                continue;
            }

            if (($i + 1) % 1000 === 0) {
                $this->info("...processed ".($i + 1));
            }
        }

        $this->info("Done. Inserted={$ins}, Updated={$upd}, Skipped(data FK)={$skipDataFk}, Skipped(bad date)={$skipDate}, QueryErrors={$qErr}");
        return 0;
    }
}
