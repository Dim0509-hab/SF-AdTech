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
    Schema::create('conversions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('offer_id')->constrained()->onDelete('cascade');
        $table->foreignId('webmaster_id')->nullable()->constrained('users')->onDelete('set null');
        $table->decimal('amount', 10, 2);
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('conversions');
}

};
