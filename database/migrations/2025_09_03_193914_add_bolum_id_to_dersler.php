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
        Schema::table('dersler', function (Blueprint $table) {
            $table->foreignId('bolum_id')->change();
            $table->foreignId('kurs_id')->change();
            $table->index(['bolum_id', 'sira']);
            $table->index(['kurs_id', 'sira']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dersler', function (Blueprint $table) {
            //
        });
    }
};
