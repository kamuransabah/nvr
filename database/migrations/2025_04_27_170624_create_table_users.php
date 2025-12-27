<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sistem adı ya da tam ad
            $table->string('isim'); // İsim
            $table->string('soyisim'); // Soyisim
            $table->string('email')->unique();
            $table->string('telefon')->nullable();
            $table->string('profil_resmi')->nullable();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->ipAddress('kayit_ip')->nullable();
            $table->timestamp('son_giris_tarihi')->nullable();
            $table->ipAddress('son_giris_ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
