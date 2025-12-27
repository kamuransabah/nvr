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
        Schema::create('siparisler', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ogrenci
            $table->unsignedBigInteger('personel_id')->nullable(); // CRM personeli
            $table->string('siparis_no')->unique();
            $table->decimal('toplam_tutar', 10, 2);
            $table->decimal('indirim_tutari', 10, 2)->default(0);
            $table->decimal('odenecek_tutar', 10, 2);
            $table->integer('odeme_durum'); // settings.type = odeme_durum
            $table->integer('odeme_turu')->nullable(); // settings.type = odeme_yontemi
            $table->integer('satis_kaynak'); // settings.type = kanal
            $table->integer('durum')->default(1); // settings.type = siparis_durum (örneğin: 1 = onaylandı)
            $table->string('ip_adresi')->nullable();
            $table->timestamp('odeme_tarihi')->nullable();
            $table->text('personel_notu')->nullable(); // eski "not" alanı yerine
            $table->timestamps(); // created_at = sipariş tarihi
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_siparisler');
    }
};
