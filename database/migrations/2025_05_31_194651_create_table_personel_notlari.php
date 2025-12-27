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
        Schema::create('personel_notlari', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personel_id')->default(0);
            $table->unsignedBigInteger('item_id')->default(0);
            $table->string('type', 50);
            $table->text('icerik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_personel_notlari');
    }
};
