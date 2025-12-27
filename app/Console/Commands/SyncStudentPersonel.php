<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

// php artisan sync:student-personel --offset=0 --limit=10000
class SyncStudentPersonel extends Command
{
    protected $signature = 'sync:student-personel {--offset=0} {--limit=1000}';
    protected $description = 'CI3 den gelen ogrenci cm_id alanını Laravel ogrenciler.personel_id alanına aktarır.';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_user/{$offset}/{$limit}";
        $this->info("Veriler çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("API isteği başarısız oldu.");
            return;
        }

        $users = $response->json();

        if (empty($users)) {
            $this->info("Bu parçada işlenecek veri yok.");
            return;
        }

        foreach ($users as $row) {
            if ((int)$row['yetki'] !== 4) {
                continue; // sadece öğrenciler
            }

            $updated = DB::table('ogrenciler')
                ->where('id', $row['id'])
                ->update([
                    'personel_id' => $row['cm_id'],
                    'updated_at' => now(),
                ]);

            //$this->line("👨‍🎓 Ogrenci ID {$row['id']} → personel_id = {$row['cm_id']}" . ($updated ? " [✓]" : " [•]"));
        }

        $this->info("Güncelleme tamamlandı (offset: $offset).");
    }
}
