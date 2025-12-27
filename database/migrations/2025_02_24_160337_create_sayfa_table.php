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
        Schema::create('sayfa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori_id')->default(0);
            $table->string('permalink', 250);
            $table->string('baslik', 250);
            $table->longText('icerik');
            $table->string('resim', 250)->nullable();
            $table->string('seo_title', 250);
            $table->string('seo_description', 250);
            $table->boolean('durum')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sayfa');
    }
};
