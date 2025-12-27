<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

// php artisan import:userdurum --offset=0 --limit=10000
class ImportUserDurum extends Command
{
    protected $signature = 'import:userdurum {--offset=0} {--limit=1000}';
    protected $description = 'CRM sisteminden kullanıcı durumlarını çekip Laravel tarafını günceller.';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/export/crm_user/{$offset}/{$limit}";
        $this->info("Durumlar çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("API isteği başarısız oldu!");
            return;
        }

        $users = $response->json();

        if (empty($users)) {
            $this->info("Aktarılacak kullanıcı verisi bulunamadı.");
            return;
        }

        foreach ($users as $row) {
            $yetki = (int) $row['yetki'];
            $durum = (int) $row['durum'];
            $id = (int) $row['id'];

            if ($yetki === 4) {
                // Öğrenci
                $updated = DB::table('ogrenciler')
                    ->where('id', $id)
                    ->update(['durum' => $durum]);

                //$this->line("👨‍🎓 Ogrenci ID $id -> durum = $durum" . ($updated ? " [✓]" : " [•]"));
            } else {
                // Users
                $updated = DB::table('users')
                    ->where('id', $id)
                    ->update(['durum' => $durum]);

                //$this->line("👤 User ID $id -> durum = $durum" . ($updated ? " [✓]" : " [•]"));
            }
        }

        $this->info("Durum güncelleme tamamlandı (offset: $offset).");
    }
}
