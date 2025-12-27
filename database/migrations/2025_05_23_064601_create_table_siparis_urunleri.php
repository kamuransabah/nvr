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
        Schema::create('siparis_urunleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siparis_id');
            $table->integer('satis_turu')->default(2);
            $table->unsignedBigInteger('urun_id');
            $table->integer('adet')->default(1);
            $table->decimal('birim_fiyat', 10, 2);
            $table->decimal('toplam', 10, 2);
            $table->timestamps();

            $table->foreign('siparis_id')->references('id')->on('siparisler')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_siparis_urunleri');
    }
};
