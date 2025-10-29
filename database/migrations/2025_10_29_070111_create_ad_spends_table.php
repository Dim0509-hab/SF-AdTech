<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('ad_spends', function (Blueprint $table) {
        $table->id();
        $table->foreignId('offer_id')->constrained();
        $table->date('date');
        $table->decimal('amount', 10, 2); // сумма затрат
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('ad_spends');
}

};
