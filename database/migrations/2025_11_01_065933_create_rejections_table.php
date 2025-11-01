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
        Schema::create('rejections', function (Blueprint $table) {
    $table->id();
    $table->foreignId('webmaster_id')->constrained('users');
    $table->foreignId('offer_id')->constrained();
    $table->text('reason'); // причина отказа
    $table->timestamp('rejected_at');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rejections');
    }
};
