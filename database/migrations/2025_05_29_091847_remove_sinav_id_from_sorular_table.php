<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sorular', function (Blueprint $table) {
            $table->dropForeign(['sinav_id']); // önce foreign key
            $table->dropColumn('sinav_id');    // sonra sütun
        });
    }

    public function down(): void
    {
        Schema::table('sorular', function (Blueprint $table) {
            $table->unsignedBigInteger('sinav_id')->nullable();

            $table->foreign('sinav_id')
                ->references('id')->on('sinavlar')
                ->onDelete('cascade');
        });
    }
};
