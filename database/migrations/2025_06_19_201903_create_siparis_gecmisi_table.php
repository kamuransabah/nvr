<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('siparis_gecmisi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siparis_id')->constrained('siparisler')->onDelete('cascade');
            $table->foreignId('personel_id')->nullable()->constrained('users')->onDelete('set null'); // veya personeller tablosu
            $table->string('durum'); // settings.key ile eşleşecek
            $table->text('personel_notu')->nullable();

            $table->timestamps(); // created_at = işlem tarihi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siparis_gecmisi');
    }
};
