<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->timestamp('yayin_tarihi')->default(now())->after('durum');
        });
    }

    public function down()
    {
        Schema::table('blog', function (Blueprint $table) {
            $table->dropColumn('yayin_tarihi');
        });
    }
};
