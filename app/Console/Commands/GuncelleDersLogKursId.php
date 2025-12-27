<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/*
 * uygulama komutu php artisan log:update-kurs-id --offset=0 --limit=10000
 * */

class GuncelleDersLogKursId extends Command {
    protected $signature = 'log:update-kurs-id  {--offset=0} {--limit=10000}';
    protected $description = 'Ders logları tablosundaki kurs_id değerini ders_id üzerinden günceller';

    public function handle()
    {
        $offset = (int) $this->option('offset') ?? 0;
        $limit = (int) $this->option('limit') ?? 100;

        $loglar = DB::table('ders_loglari')
            ->select('id', 'ders_id')
            ->where('kurs_id', 0)
            ->orderBy('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        $bar = $this->output->createProgressBar($loglar->count());
        $bar->start();

        foreach ($loglar as $log) {
            $kursId = DB::table('dersler')
                ->where('id', $log->ders_id)
                ->value('kurs_id');

            if ($kursId) {
                DB::table('ders_loglari')
                    ->where('id', $log->id)
                    ->update(['kurs_id' => $kursId]);

                //$this->line("Güncellendi: log_id={$log->id}, kurs_id={$kursId}");
            } else {
                $this->warn("Kurs bulunamadı: log_id={$log->id}, ders_id={$log->ders_id}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('İşlem tamamlandı.');
    }

}
