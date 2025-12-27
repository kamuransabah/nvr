<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

// php artisan import:users --offset=30000 --limit=10000

class ImportUser extends Command
{
    protected $signature = 'import:users {--offset=0} {--limit=1000}';
    protected $description = 'CI3 kullanıcılarını Laravel sistemine aktarır';

    public function handle()
    {
        $offset = (int) $this->option('offset');
        $limit = (int) $this->option('limit');

        $url = "https://www.novarge.com.tr/bridge/user/{$offset}/{$limit}";
        $this->info("Veri çekiliyor: $url");

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->failed()) {
            $this->error("Veri alınamadı!");
            return;
        }

        $users = $response->json();

        if (empty($users)) {
            $this->info("Bu parçada aktarılacak veri bulunamadı.");
            return;
        }

        foreach ($users as $row) {
            $is_ogrenci = ($row['yetki'] == 4);

            if ($is_ogrenci) {

                $mezuniyetKey = DB::table('settings')
                    ->where('type', 'mezuniyet')
                    ->where('value', $row['mezuniyet'])
                    ->value('key');

                DB::table('ogrenciler')->insertOrIgnore([
                    'id' => $row['id'],
                    'personel_id' => $row['cm_id'],
                    'isim' => $row['isim'],
                    'soyisim' => $row['soyisim'],
                    'email' => $row['eposta'],
                    'telefon' => $row['telefon'],
                    'password' => $row['sifre'],
                    'tc_kimlik_no' => $row['tc_no'],
                    'kaynak' => $row['kaynak'],
                    'kayit_ip' => $row['ip_adresi'],
                    'son_giris_tarihi' => $row['son_giris'],
                    'son_giris_ip' => $row['son_ip_adresi'],
                    'cinsiyet' => isset($row['cinsiyet']) ? ($row['cinsiyet'] == 'K' ? 'kadin' : 'erkek') : null,
                    'dogum_tarihi' => $row['dogum_tarihi'] ?? null,
                    'mezuniyet' => $mezuniyetKey ?? null,
                    'meslek' => $row['meslek'] ?? null,
                    'adres' => $row['adres'] ?? null,
                    'il_id' => $row['il'] ?? null,
                    'ilce_id' => $row['ilce'] ?? null,
                    'profil_resmi' => $row['resim'] ?? null,
                    'created_at' => $row['kayit_tarihi'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('users')->insertOrIgnore([
                    'id' => $row['id'],
                    'name' => $row['isim'] . ' ' . $row['soyisim'],
                    'isim' => $row['isim'],
                    'soyisim' => $row['soyisim'],
                    'email' => $row['eposta'],
                    'telefon' => $row['telefon'],
                    'password' => $row['sifre'],
                    'profil_resmi' => $row['resim'] ?? null,
                    'kayit_ip' => $row['ip_adresi'],
                    'son_giris_tarihi' => $row['son_giris'],
                    'son_giris_ip' => $row['son_ip_adresi'],
                    'created_at' => $row['kayit_tarihi'],
                    'updated_at' => now(),
                ]);

                $user = User::find($row['id']);
                if ($user) {
                    switch ($row['yetki']) {
                        case 1:
                            $user->assignRole('admin');
                            break;
                        case 3:
                            $user->assignRole('personel');
                            break;
                        case 5:
                            $user->assignRole('egitmen');
                            break;
                        case 12:
                            $user->assignRole('kurum');
                            break;
                        default:
                            // Diğer yetki türleri atlanır
                            break;
                    }
                }

                // Personel
                if ($row['yetki'] == 3) {
                    DB::table('personel')->insertOrIgnore([
                        'user_id' => $row['id'],
                        'sirket_telefon' => $row['telefon'],
                        'sirket_email' => $row['eposta'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Eğitmen
                if ($row['yetki'] == 5) {
                    DB::table('egitmen')->insertOrIgnore([
                        'user_id' => $row['id'],
                        'ozgecmis' => $row['ozgecmis'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!empty($row['kurs_id'])) {
                        $kurslar = explode(',', $row['kurs_id']);
                        foreach ($kurslar as $kursId) {
                            DB::table('egitmen_kurs')->insertOrIgnore([
                                'user_id' => $row['id'],
                                'kurs_id' => trim($kursId),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Kurum
                if ($row['yetki'] == 12) {
                    DB::table('kurum')->insertOrIgnore([
                        'user_id' => $row['id'],
                        'kurum_adi' => $row['isim'] . ' ' . $row['soyisim'],
                        'kurum_telefon' => $row['telefon'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->info("Aktarım tamamlandı (offset: $offset).");
    }
}
