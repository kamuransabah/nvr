<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinavlar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kurs_id');
            $table->string('sinav_adi', 50);
            $table->date('sinav_tarih')->nullable();
            $table->time('sinav_saat')->nullable();
            $table->string('sinav_yer', 250)->nullable();
            $table->integer('sinav_sure');
            $table->tinyInteger('tur');
            $table->date('baslangic_tarihi');
            $table->date('bitis_tarihi');
            $table->integer('sira')->default(0);
            $table->tinyInteger('otosinav')->default(1);
            $table->tinyInteger('durum')->default(1);
            $table->timestamps();
        });

        Schema::create('sorular', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinav_id');
            $table->unsignedBigInteger('kurs_id');
            $table->unsignedBigInteger('bolum');
            $table->text('soru');
            $table->string('cevap', 50);
            $table->tinyInteger('durum')->default(1);
            $table->timestamps();

            $table->foreign('sinav_id')->references('id')->on('sinavlar')->onDelete('cascade');
        });

        Schema::create('secenekler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('soru_id');
            $table->string('harf', 2);
            $table->string('secenek');
            $table->string('resim')->nullable();
            $table->boolean('dogru_mu')->default(false);
            $table->timestamps();

            $table->foreign('soru_id')->references('id')->on('sorular')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('secenekler');
        Schema::dropIfExists('sorular');
        Schema::dropIfExists('sinavlar');
    }
};
