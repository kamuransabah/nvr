<?php

// database/migrations/2025_09_17_000000_create_sinav_sorulari_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinav_sorulari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sinav_id');
            $table->unsignedBigInteger('soru_id');
            $table->unsignedInteger('sira');                 // JSON index + 1
            $table->decimal('puan', 6, 2)->default(1);       // isteğe göre değiştirilebilir
            $table->timestamps();

            $table->foreign('sinav_id')->references('id')->on('sinavlar')->onDelete('cascade');
            $table->foreign('soru_id')->references('id')->on('sorular')->onDelete('restrict');

            $table->unique(['sinav_id','soru_id']);
            $table->index(['sinav_id','sira']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinav_sorulari');
    }
};
