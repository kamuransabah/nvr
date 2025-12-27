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
        Schema::create('dersler', function (Blueprint $table) {
            $table->integer('id', 10)->autoIncrement();
            $table->integer('kurs_id', false, true)->length(5);
            $table->integer('egitmen_id', false, true)->length(5);
            $table->integer('demo', false, true)->length(1)->default(0);
            $table->string('permalink', 250);
            $table->string('baslik', 250);
            $table->text('ozet');
            $table->longText('icerik')->nullable();
            $table->integer('sure')->nullable();
            $table->string('video_kaynak_id', 100)->nullable();
            $table->string('dosya', 250)->nullable();
            $table->string('resim', 250)->nullable();
            $table->dateTime('tarih');
            $table->dateTime('tarih_guncelleme')->nullable();
            $table->integer('hit', false, true)->length(10);
            $table->integer('sira', false, true)->length(5);
            $table->integer('durum', false, true)->length(1);

            $table->primary('id');
            $table->index('id');
            $table->index('kurs_id');
            $table->index('egitmen_id');
            $table->index('durum');
            $table->index('demo');
        });
         * */
        Schema::create('dersler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kurs_id', false, true)->length(10);
            $table->unsignedBigInteger('egitmen_id', false, true)->length(10);
            $table->tinyInteger('demo', false, true)->default(0);
            $table->string('permalink', 250);
            $table->string('baslik', 250);
            $table->text('ozet');
            $table->longText('icerik')->nullable();
            $table->integer('sure')->nullable();
            $table->string('video_kaynak_id', 100)->nullable();
            $table->string('dosya', 250)->nullable();
            $table->string('resim', 250)->nullable();
            $table->integer('sira', false, true)->length(5);
            $table->tinyInteger('durum', false, true)->default(1);
            $table->timestamps();

            $table->primary('id');
            $table->index('id');
            $table->index('kurs_id');
            $table->index('egitmen_id');
            $table->index('durum');
            $table->index('demo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_dersler');
    }
};
