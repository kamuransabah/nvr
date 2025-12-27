<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('blog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kategori')->default(0)->index();
            $table->string('permalink', 250);
            $table->text('kurs_id');
            $table->string('baslik', 250);
            $table->text('ozet');
            $table->longText('icerik');
            $table->string('resim', 250);
            $table->string('detay_resim', 250);
            $table->dateTime('tarih');
            $table->unsignedInteger('hit')->default(0);
            $table->string('seo_title', 250);
            $table->string('seo_description', 250);
            $table->unsignedTinyInteger('tur')->default(0)->nullable();
            $table->unsignedTinyInteger('durum')->default(1);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog');
    }
};
