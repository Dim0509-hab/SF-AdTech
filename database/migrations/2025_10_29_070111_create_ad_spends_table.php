<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdSpendsTable extends Migration
{
    public function up()
    {
        Schema::create('ad_spends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('offer_id')
                ->references('id')->on('offers')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_spends');
    }
}

