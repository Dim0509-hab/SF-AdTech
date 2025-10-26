<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
 Schema::create('offer_webmaster', function (Blueprint $table) {
 $table->foreignId('webmaster_id')->constrained('users')->onDelete('cascade');
 $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
 $table->decimal('agreed_price', 10, 2); // если нужна цена
 $table->timestamps();

 $table->primary(['webmaster_id', 'offer_id']); // составной первичный ключ
 });
}

public function down()
{
 Schema::dropIfExists('offer_webmaster');
}

};
