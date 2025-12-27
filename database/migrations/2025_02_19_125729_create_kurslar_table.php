<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('kurslar', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kategori')->default(0);
            $table->string('kurs_adi', 255);
            $table->string('permalink', 255);
            $table->text('ozet');
            $table->longText('aciklama');
            $table->text('neler_ogrenecegim')->nullable();
            $table->text('gereksinimler')->nullable();
            $table->text('kurs_icerigi')->nullable();
            $table->unsignedTinyInteger('kurs_puani')->nullable();
            $table->string('label', 50)->nullable();
            $table->unsignedInteger('fiyat')->nullable();
            $table->enum('ucretsiz', ['E', 'H'])->default('H');
            $table->string('egitim_suresi', 50)->nullable();
            $table->string('egitim_sureci', 50)->nullable();
            $table->string('sertifika', 250)->nullable();
            $table->string('kitap_destegi', 50)->nullable();
            $table->unsignedTinyInteger('sinav_basari_orani')->nullable();
            $table->unsignedTinyInteger('ders_sayisi')->nullable();
            $table->string('egitim_seviyesi', 50)->nullable();
            $table->text('belgeler')->nullable();
            $table->string('resim', 250)->nullable();
            $table->string('sertifika_ornegi', 250)->nullable();
            $table->unsignedInteger('sira')->default(0);
            $table->unsignedTinyInteger('tur')->default(0);
            $table->string('seo_title', 250);
            $table->string('seo_description', 250);
            $table->unsignedTinyInteger('durum')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kurslar');
    }
};
