<?php

// app/Console/Commands/ImportSinavSorulari.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/*
 * Uygulama Komutu
  php artisan import:sinav-sorulari --offset=0 --limit=10000
 * */

class ImportSinavSorulari extends Command
{
    protected $signature = 'import:sinav-sorulari {--offset=0} {--limit=1000}';
    protected $description = 'Eski sistemden sinav.sorular JSON’unu sinav_sorulari tablosuna aktarır (sıra korunur).';

    public function handle(): int
    {
        $offset = (int)$this->option('offset');
        $limit  = (int)$this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/sinav/{$offset}/{$limit}";
        $this->info("GET $url");

        $res = Http::withOptions(['verify' => false])->get($url);
        if ($res->failed()) {
            $this->error('API isteği başarısız.');
            return self::FAILURE;
        }

        $items = $res->json();
        if (empty($items)) {
            $this->warn('Aktarılacak kayıt yok.');
            return self::SUCCESS;
        }

        // 1) Mevcut SINAV ID’lerini önceden al (tek sefer)
        $existingExamIds = DB::table('sinavlar')->pluck('id')->toArray();
        $existingExamSet = array_fill_keys($existingExamIds, true);

        // 2) Bu batch içindeki TÜM soru_id’leri topla → tek sorguyla var olanları çek
        $allQuestionIds = [];
        foreach ($items as $row) {
            $json = $row['sorular'] ?? null;
            if (empty($json)) continue;
            $decoded = is_string($json) ? json_decode($json, true) : $json;
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) continue;

            foreach ($decoded as $qid) {
                $qid = (int)$qid;
                if ($qid > 0) $allQuestionIds[$qid] = true;
            }
        }
        $allIds = array_keys($allQuestionIds);
        $existingQuestionIds = empty($allIds)
            ? []
            : DB::table('sorular')->whereIn('id', $allIds)->pluck('id')->toArray();
        $existingQuestionSet = array_fill_keys($existingQuestionIds, true);

        $toplamInsert = 0;
        $skipNoExam = 0;
        $skipInvalidJson = 0;
        $skipEmpty = 0;
        $skipNoQuestion = 0;

        foreach ($items as $row) {
            $sinavId = $row['id'] ?? null;
            $json    = $row['sorular'] ?? null;

            // SINAV var mı?
            if (!$sinavId || !isset($existingExamSet[$sinavId])) {
                $skipNoExam++;
                $this->warn("Atlandı (sinav yok): sinav_id={$sinavId}");
                continue;
            }

            // JSON var mı/geçerli mi?
            if (empty($json)) {
                $skipEmpty++;
                $this->warn("Atlandı (sorular boş/null): sinav_id={$sinavId}");
                continue;
            }
            $decoded = is_string($json) ? json_decode($json, true) : $json;
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
                $skipInvalidJson++;
                $this->warn("Atlandı (geçersiz JSON): sinav_id={$sinavId}");
                continue;
            }

            // Sıralı satırlar: SADECE var olan soru_id’leri al
            $rows = [];
            $validCount = 0;

            foreach (array_values($decoded) as $i => $soruId) {
                $soruId = (int)$soruId;
                if ($soruId > 0 && isset($existingQuestionSet[$soruId])) {
                    $rows[] = [
                        'sinav_id'   => (int)$sinavId,
                        'soru_id'    => $soruId,
                        'sira'       => $i + 1,
                        'puan'       => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $validCount++;
                }
            }

            if ($validCount === 0) {
                $skipNoQuestion++;
                $this->warn("Atlandı (eşleşen soru yok): sinav_id={$sinavId}");
                continue;
            }

            DB::table('sinav_sorulari')->upsert(
                $rows,
                ['sinav_id', 'soru_id'],
                ['sira', 'puan', 'updated_at']
            );

            $this->info("Aktarıldı: sinav_id={$sinavId} ({$validCount} soru)");
            $toplamInsert += $validCount;
        }

        $this->info("Bitti. Toplam eklenen/güncellenen satır: {$toplamInsert}");
        $this->line("Özet → yok sinav: {$skipNoExam}, boş/null: {$skipEmpty}, geçersiz JSON: {$skipInvalidJson}, eşleşen soru yok: {$skipNoQuestion}");

        return self::SUCCESS;
    }
}
