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
    Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('offer_id')->constrained()->onDelete('cascade');
        $table->foreignId('webmaster_id')->constrained('users')->onDelete('cascade');
        // Другие поля (например, статус, дата подписки и т. п.)
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('subscriptions');
}

};
