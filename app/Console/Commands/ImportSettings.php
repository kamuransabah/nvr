<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSettings extends Command
{
    protected $signature = 'import:settings';
    protected $description = 'CodeIgniter 3 config ayarlarını veritabanına aktarır.';

    public function handle()
    {

        $config['mezuniyet'] = array('İlkokul', 'Ortaokul', 'Lise', 'Ön Lisans', 'Lisans', 'Yüksek Lisans');
        $config['data_durum'] = array('1'=>'Yeni', '2'=> 'Aktif', '3' => 'Olumsuz', '4' => 'Çöp', '5' => 'Kayıt', '6' => 'Randevu');
        $config['data_tur'] = array('0' => 'Yeni Data', '1'=>'Eski Data');
        $config['satis_turu'] = array('1'=>'Kurs', '2' => 'Ürün', '3' => 'Sınav Harcı');
        $config['odeme_turu'] = array('1'=>'Kredi Kartı', '2' => 'Havale / EFT', '3' => 'Taksit', '4' => 'Nakit', '5' => 'İndirim Kodu', '6' => 'Ücretsiz');
        $config['odeme_durum'] = array('0'=>'Ödenmedi', '1' => 'Ödendi', '2' => 'Onay Sürecinde', '3' => 'Red Edildi');
        $config['sinav_tercihi'] = array('1'=>'MEB', '2' => 'Online', '3' => 'Her İkisi');
        $config['sinav_turu'] = array('1'=>'MEB Sertifika Sınavı', '2' => 'Uluslararası Sertifika Sınavı', '3' => 'Deneme Sınavı');
        $config['sinav_durum'] = array('0' => 'Sınava Girecek', '1' => 'Sınava Girdi', '2' => 'Sınava Girmedi');
        $config['sinav_sonuc'] = array('0' => 'Sınava Girecek', '1' => 'Geçti', '2' => 'Kaldı');
        $config['belge_turleri'] = array('1'=>'Kimlik Fotokopisi', '2' => 'Diploma veya Öğrenci Belgesi', '3' => 'Vesikalık Fotoğraf', '4' => 'Sağlık Raporu', '5' => 'İkametgâh Belgesi', '6' => 'Adli Sicil Belgesi');
        $config['belge_durum'] = array('1'=>'İnceleniyor', '2' => 'Onaylandı', '3' => 'Onaylanmadı');
        $config['kategori_turu'] = array('kurs' => 'Kurs', 'blog' => 'Blog', 'icerik' => 'İçerik', 'urun' => 'Ürün', 'sss'=> 'Sıkça Sorulan Sorular');
        $config['menu_turu'] = array('footer'=>'Footer Menü', 'header' => 'Ana Menü', 'giris' => 'Giriş Sayfası');
        $config['satis_kaynak'] = array('1'=>'DATA', '2'=>'CRM', '3'=>'WEB');
        $config['ticket_kategori'] = array('muhasebe'=>'Muhasebe', 'teknik'=>'Teknik Destek', 'egitim'=>'Eğitimler', 'diger' => 'Diğer', 'sozlesme' => 'Sözlesme İptal ve İade');
        $config['ticket_durum'] = array('1'=>'Açık', '2'=>'Kapalı', '3'=>'Pasif');
        $config['ogrenci_kaynak'] = array('0' => 'Bilinmiyor', '1'=>'DATA', '2'=>'CRM', '3'=>'WEB', '4' => 'ESKİ ÜYE');
        $config['emojiler'] = array('1' => 'Beğen', '2'=> 'Mutlu', '3' => 'İnanılmaz', '4' => 'Muhteşem', '5' => 'Üzgün', '6' => 'Kızgın');
        $config['ik_durum'] = array('0' => 'Yeni', '1'=> 'İşe Alındı', '2' => 'Olumsuz', '3' => 'Ön Görüşme Randevu');
        $config['duyuru_turu'] = array('1' => 'Header', '2'=> 'Öğrenciler');
        $config['reklam'] = array('blog_detay' => 'Blog Detay');
        $config['yorum_turu'] = array('blog' => 'Blog', 'kurs'=> 'Kurs');
        $config['kargo_durumu'] = array('0' => 'Onay Bekleniyor', '1' => 'Online Satış', '2'=> 'Kargoya Hazırlanıyor', '3' => 'Kargoya Verildi', '4' => 'Teslim Edildi');
        $config['yasak_turu'] = array('telefon'=>'Telefon', 'eposta' => 'E-Posta', 'tc_no' => 'T.C No');
        $config['sertifika_turu'] = array('0' => 'Bilinmiyor','1'=>'MEB', '2' => 'Uluslararası', '3' => 'THK Ünv.', '4' => 'Ankara Bilim Ünv.');
        $config['sertifika_belge_turu'] = array(
            '1' => 'Sertifika',
            '2' => 'Katılım Belgesi',
            '3' => 'Üniversite Sertifika',
            '4' => 'MEB Sertifika',
            '5' => 'Uluslararası Sertifika',
            '6' => 'Katılım Belgesi',
            '7' => 'Başarı Belgesi',
            '8' => 'Üstün Başarı Belgesi',
            '9' => 'Examplar Global',
        );

        foreach ($config as $type => $items) {
            foreach ($items as $key => $value) {
                DB::table('settings')->insert([
                    'type' => $type,
                    'key' => is_numeric($key) ? (string) $key : $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->info('Ayarlar başarıyla veritabanına aktarıldı!');
    }
}
