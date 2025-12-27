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
        Schema::create('egitmen_kazanclari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('urun_id');
            $table->unsignedBigInteger('egitmen_id');
            $table->decimal('komisyon_orani', 5, 2)->nullable(); // örn: %15
            $table->decimal('komisyon_tutari', 10, 2)->nullable();
            $table->integer('odeme_durum')->default(0); // settings.type = egitmen_odeme_durum
            $table->boolean('islenmis')->default(false); // eski egitmen_hesap alanı
            $table->timestamp('odeme_tarihi')->nullable();
            $table->timestamps();

            $table->foreign('urun_id')->references('id')->on('siparis_urunleri')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_egitmen_kazanclari');
    }
};
