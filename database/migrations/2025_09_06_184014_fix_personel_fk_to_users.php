<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // data_gorusmeler: personel_id -> users(id)
        Schema::table('data_gorusmeler', function (Blueprint $table) {
            // Eski FK’yi düşür
            $table->dropForeign('data_gorusmeler_personel_id_foreign'); // gerekirse adı aşağıdaki notta anlatıldığı gibi kontrol et
            // Yeni FK: users
            $table->foreign('personel_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });

        // data_randevular: personel_id -> users(id)
        Schema::table('data_randevular', function (Blueprint $table) {
            // Eski FK’yi düşür
            $table->dropForeign('data_randevular_personel_id_foreign');
            // Yeni FK: users
            $table->foreign('personel_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Geri alma: tekrar personel tablosuna (eski durum)
        Schema::table('data_gorusmeler', function (Blueprint $table) {
            $table->dropForeign('data_gorusmeler_personel_id_foreign');
            $table->foreign('personel_id')
                ->references('id')->on('personel')
                ->nullOnDelete();
        });

        Schema::table('data_randevular', function (Blueprint $table) {
            $table->dropForeign('data_randevular_personel_id_foreign');
            $table->foreign('personel_id')
                ->references('id')->on('personel')
                ->nullOnDelete();
        });
    }
};
