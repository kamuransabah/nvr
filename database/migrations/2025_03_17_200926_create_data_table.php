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
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('kurs_id')->default(0)->index();
            $table->tinyInteger('personel_id')->default(0)->index();
            $table->tinyInteger('kaynak')->default(0);
            $table->string('isim', 50);
            $table->string('sehir', 50);
            $table->string('eposta', 150);
            $table->string('telefon', 50)->nullable();
            $table->dateTime('basvuru_tarihi')->default(now());
            $table->dateTime('atama_tarihi')->nullable()->default(null);
            $table->tinyInteger('olumsuz_id')->default(0);
            $table->tinyInteger('cevapsiz')->default(0);
            $table->unsignedTinyInteger('durum')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
