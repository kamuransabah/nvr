<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siparis_kargo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siparis_id')->constrained('siparisler')->onDelete('cascade')->unique();

            $table->string('kargo_firma')->nullable();       // Örnek: Yurtiçi, MNG, Aras
            $table->string('kargo_takip_no')->nullable();     // Örnek: 123456789
            $table->timestamp('kargo_tarih')->nullable();     // Gönderim zamanı
            $table->text('aciklama')->nullable();             // Ek bilgi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siparis_kargo');
    }
};
