<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ogrenciler', function (Blueprint $table) {
            $table->id();
            $table->string('isim');
            $table->string('soyisim');
            $table->string('email')->unique();
            $table->string('telefon')->nullable();
            $table->string('password');
            $table->string('tc_kimlik_no')->nullable();
            $table->enum('cinsiyet', ['erkek', 'kadin'])->nullable();
            $table->date('dogum_tarihi')->nullable();
            $table->string('kaynak')->nullable(); // Nereden kayıt oldu (web, CRM, davet vs.)
            $table->string('mezuniyet')->nullable();
            $table->string('meslek')->nullable();
            $table->text('adres')->nullable();
            $table->unsignedBigInteger('il_id')->nullable();
            $table->unsignedBigInteger('ilce_id')->nullable();
            $table->string('profil_resmi')->nullable();
            $table->ipAddress('kayit_ip')->nullable();
            $table->timestamp('son_giris_tarihi')->nullable();
            $table->ipAddress('son_giris_ip')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ogrenciler');
    }
};
