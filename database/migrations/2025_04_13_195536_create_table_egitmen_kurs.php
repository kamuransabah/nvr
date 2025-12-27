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
        Schema::create('egitmen_kurs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');     // eğitmen (users tablosu)
            $table->unsignedBigInteger('kurs_id');     // kurs (kurslar tablosu)

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kurs_id')->references('id')->on('kurslar')->onDelete('cascade');

            $table->unique(['user_id', 'kurs_id']); // aynı kursa aynı eğitmen 2 kez atanmasın
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_egitmen_kurs');
    }
};
