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
        Schema::create('bolumler', function (Blueprint $table) {


            $table->id();
            $table->foreignId('kurs_id')->constrained('kurslar')->onDelete('cascade')->unique();

            $table->string('bolum_adi')->nullable();
            $table->string('permalink')->nullable();
            $table->text('aciklama')->nullable();
            $table->integer('sira')->nullable()->default(1);
            $table->tinyInteger('durum')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_bolumler');
    }
};
