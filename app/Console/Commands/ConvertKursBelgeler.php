<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kurs;

class ConvertKursBelgeler extends Command
{
    protected $signature = 'convert:kurs-belgeler';
    protected $description = 'kurslar.belgeler alanını serialize formatından JSON array formatına çevirir';

    public function handle()
    {
        $this->info('Dönüştürme başladı…');

        Kurs::select('id', 'kurs_icerigi')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $raw = (string) $row->getRawOriginal('kurs_icerigi'); // veritabanındaki ham değer

                // serialize -> array (boş veya geçersizse [])
                $arr = convertSerializeToJson($raw); // daha önce yazdığımız helper

                // JSON olarak kaydet (boş ise [] yazılır)
                $row->kurs_icerigi = $arr;
                $row->save();
            }
        });

        $this->info('Dönüştürme tamamlandı.');
        return 0;
    }
}
