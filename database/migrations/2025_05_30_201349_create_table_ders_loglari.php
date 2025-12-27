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
        /*
         *
 Schema::create('table_ders_loglari', function (Blueprint $table) {
     $table->id();
     $table->integer('ogrenci_id');
     $table->integer('ders_id');
     $table->dateTime('ilk_izleme');
     $table->dateTime('son_izleme');
     $table->string('ip_adresi_ilk', 50);
     $table->string('ip_adresi_son', 50);
     $table->integer('izledigi_sure')->default(0);
     $table->string('nerede_kaldi', 50);
     $table->index('id');
     $table->index('ogrenci_id');
     $table->index('ders_id');
 });
         * */
        Schema::create('ders_loglari', function (Blueprint $table) {
            $table->id();
            $table->integer('ogrenci_id');
            $table->integer('ders_id');
            $table->dateTime('ilk_izleme');
            $table->dateTime('son_izleme');
            $table->string('ip_adresi_ilk', 50);
            $table->string('ip_adresi_son', 50);
            $table->integer('izledigi_sure')->default(0);
            $table->string('nerede_kaldi', 50);
            $table->index('id');
            $table->index('ogrenci_id');
            $table->index('ders_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_ders_loglari');
    }
};
