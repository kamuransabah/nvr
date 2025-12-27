<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ogrenci_kurslari', function (Blueprint $table) {
            $table->unsignedBigInteger('ogrenci_id')->after('id');

            $table->foreign('ogrenci_id')
                ->references('id')->on('ogrenciler')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ogrenci_kurslari', function (Blueprint $table) {
            $table->dropForeign(['ogrenci_id']);
            $table->dropColumn('ogrenci_id');
        });
    }
};
