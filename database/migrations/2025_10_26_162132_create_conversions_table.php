<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->decimal('amount', 10, 2); // сумма конверсии
            $table->string('transaction_id')->nullable(); // ID транзакции
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
        Schema::dropIfExists('conversions');
    }
};
