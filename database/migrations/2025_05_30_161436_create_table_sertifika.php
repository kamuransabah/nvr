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
        Schema::create('sertifikalar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('kurs_id')->default(0);
            $table->tinyInteger('tur')->default(0);
            $table->string('sertifika_no', 20)->nullable();
            $table->string('dosya', 255)->nullable();
            $table->string('isim', 50)->nullable();
            $table->string('soyisim', 50)->nullable();
            $table->dateTime('tarih')->nullable();
            $table->timestamps();

            $table->index('kurs_id')->references('id')->on('kurslar');
            $table->index('user_id')->references('id')->on('ogrenciler');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikalar');
    }
};
