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
        Schema::create('kurum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // birebir ilişki

            $table->string('kurum_adi');
            $table->string('kurum_telefon')->nullable(); // farklıysa
            $table->string('kurum_logo')->nullable(); // özel logo (genellikle profil_resmi yeterlidir)

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('egitmen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            $table->text('ozgecmis')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('personel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();

            $table->string('sirket_telefon')->nullable();
            $table->string('sirket_email')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurum');
        Schema::dropIfExists('egitmen');
        Schema::dropIfExists('personel');
    }
};
