<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToBlogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog', function (Blueprint $table) {
            // Kategori alanını önce integer olarak ekleyin (eğer yoksa)
            // $table->unsignedBigInteger('kategori')->nullable();

            // Foreign key ekleyin
            $table->foreign('kategori') // 'kategori' alanını referans al
            ->references('id')      // 'kategori' tablosundaki 'id' alanına bağla
            ->on('kategori');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog', function (Blueprint $table) {
            // Foreign key'i kaldır
            $table->dropForeign(['kategori']);
        });
    }
}
