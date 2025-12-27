<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('ogrenci_sinavlari', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('sinav_id')->default(0);
            $table->unsignedBigInteger('kurs_id')->default(0);
            $table->text('cevaplar')->nullable();
            $table->tinyInteger('sonuc');
            $table->integer('puan');
            $table->integer('dogru_cevap');
            $table->integer('yalnis_cevap');
            $table->integer('bos_cevap');
            $table->tinyInteger('durum');
            $table->dateTime('sinav_tarihi');
            $table->timestamps();

            $table->foreign('kurs_id')->references('id')->on('kurslar');
            $table->foreign('user_id')->references('id')->on('ogrenciler');
            $table->foreign('sinav_id')->references('id')->on('sinavlar');


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogrenci_sinavlari');
    }
};

