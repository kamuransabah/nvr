<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_randevular', function (Blueprint $table) {
            $table->id();

            // İlişkiler
            $table->unsignedBigInteger('personel_id')->nullable(); // personel tablosu
            $table->unsignedBigInteger('data_id');                  // data tablosu
            $table->unsignedBigInteger('kurs_id')->nullable();      // kurslar tablosu (opsiyonel)

            // Alanlar
            $table->dateTime('randevu_tarihi')->index();
            $table->string('durum', 30)->default('planlandi')->index(); // planlandi|gerceklesti|gelmedi|iptal gibi

            $table->timestamps();

            // Yabancı anahtarlar
            $table->foreign('personel_id')->references('id')->on('personel')->nullOnDelete();
            $table->foreign('data_id')->references('id')->on('data')->cascadeOnDelete();
            $table->foreign('kurs_id')->references('id')->on('kurslar')->nullOnDelete();

            // Faydalı bileşik indeksler
            $table->index(['data_id', 'randevu_tarihi']);
            $table->index(['randevu_tarihi', 'durum']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_randevular');
    }
};
