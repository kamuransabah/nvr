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
        Schema::create('data_olumsuz_nedenler', function (Blueprint $table) {
            $table->id();
            $table->string('isim');                 // ör: "Yanlış Numara"
            $table->text('mesaj')->nullable();      // detaylı açıklama (opsiyonel)
            $table->timestamps();

            $table->index('isim');                  // hızlı arama/listelerde iş görür
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_olumsuz_nedenler');
    }
};
