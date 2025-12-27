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
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ust_id')->default(0)->index();
            $table->string('tur', 50)->index();
            $table->string('isim', 250);
            $table->text('aciklama');
            $table->string('permalink', 250);
            $table->unsignedInteger('sira')->default(1);
            $table->unsignedTinyInteger('durum')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
