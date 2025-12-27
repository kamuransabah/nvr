<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_gorusmeler', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->unsignedBigInteger('personel_id')->nullable();   // personel tablosu
            $table->unsignedBigInteger('data_id');                   // data tablosu
            $table->unsignedBigInteger('kurs_id')->nullable();       // kurslar tablosu
            $table->unsignedBigInteger('olumsuz_id')->nullable();    // data_olumsuz_nedenler
            $table->unsignedBigInteger('randevu_id')->nullable();    // data_randevular

            // Alanlar
            $table->tinyInteger('kayit')->nullable();        // görüşme metni / özet / log
            $table->text('personel_notu')->nullable();    // iç not

            $table->timestamps();

            // Yabancı anahtarlar
            $table->foreign('personel_id')->references('id')->on('personel')->nullOnDelete();
            $table->foreign('data_id')->references('id')->on('data')->cascadeOnDelete();
            $table->foreign('kurs_id')->references('id')->on('kurslar')->nullOnDelete();
            $table->foreign('olumsuz_id')->references('id')->on('data_olumsuz_nedenler')->nullOnDelete();
            $table->foreign('randevu_id')->references('id')->on('data_randevular')->nullOnDelete();

            // Performans için indeksler
            $table->index(['data_id', 'tarih']);
            $table->index(['personel_id', 'tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_gorusmeler');
    }
};
