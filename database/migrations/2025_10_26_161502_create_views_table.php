<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Внешний ключ
            $table->foreign('offer_id')
                  ->references('id')
                  ->on('offers')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('views');
    }
};
