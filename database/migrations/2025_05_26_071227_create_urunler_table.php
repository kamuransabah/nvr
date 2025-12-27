<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('urunler', function (Blueprint $table) {
            $table->id();
            $table->string('isim');
            $table->string('slug')->unique();
            $table->text('aciklama')->nullable();
            $table->decimal('fiyat', 10, 2)->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->string('resim')->nullable(); // public/storage içine dosya yolu
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('urunler');
    }
};
