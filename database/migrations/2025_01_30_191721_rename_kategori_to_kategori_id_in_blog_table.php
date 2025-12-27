<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameKategoriToKategoriIdInBlogTable extends Migration
{
    public function up()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->renameColumn('kategori', 'kategori_id');
        });
    }

    public function down()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->renameColumn('kategori_id', 'kategori');
        });
    }
}
