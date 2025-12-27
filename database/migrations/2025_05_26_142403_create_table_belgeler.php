<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('belgeler', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ogrenciler tablosundaki id'ye karşılık gelir
            $table->tinyInteger('tur')->default(0); // örneğin: 1=kimlik, 2=diploma, vs.
            $table->string('belge'); // dosya adı veya yolu
            $table->text('aciklama')->nullable();
            $table->tinyInteger('durum')->default(1); // örn: 1=aktif, 0=pasif
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('ogrenciler')->onDelete('cascade'); // öğrenci silinirse belgeleri de silinir
        });
    }

    public function down() {
        Schema::dropIfExists('belgeler');
    }
};
