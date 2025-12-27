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
        Schema::create('iletisim', function (Blueprint $table) {
            $table->id();
            $table->string('isim', 50);
            $table->string('soyisim', 50);
            $table->string('eposta', 150);
            $table->string('telefon', 50)->nullable();
            $table->longText('mesaj');
            $table->string('dosya', 250)->nullable();
            $table->string('ip_adresi', 50);
            $table->longText('cevap')->nullable();
            $table->unsignedTinyInteger('durum')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iletisim');
    }
};
