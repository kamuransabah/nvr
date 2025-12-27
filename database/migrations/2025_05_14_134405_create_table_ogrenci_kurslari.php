<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ogrenci_kurslari', function (Blueprint $table) {
            $table->id(); // $row['id']
            $table->unsignedBigInteger('kurs_id');
            $table->unsignedBigInteger('personel_id')->nullable(); // eski sistemde cm_id olabilir
            $table->date('tarih_baslangic')->nullable();
            $table->date('tarih_bitis')->nullable();
            $table->tinyInteger('sinav_tercihi')->nullable();
            $table->tinyInteger('sertifika_turu')->nullable();
            $table->boolean('sozlesme')->default(0);
            $table->dateTime('sozlesme_tarihi')->nullable();
            $table->ipAddress('sozlesme_ip')->nullable();
            $table->tinyInteger('durum')->default(0);

            $table->timestamps(); // created_at, updated_at

            // İlişkiler (istersen sonradan foreign key ekleyebiliriz)
            $table->foreign('kurs_id')->references('id')->on('kurslar');
            $table->foreign('personel_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogrenci_kurslari');
    }
};
