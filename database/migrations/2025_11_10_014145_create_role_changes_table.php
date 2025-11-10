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
        Schema::create('role_changes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('old_role');
    $table->string('new_role');
    $table->unsignedBigInteger('changed_by');
    $table->string('ip')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('sf_adtech.users')->onDelete('cascade');
    $table->foreign('changed_by')->references('id')->on('sf_adtech.users');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_changes');
    }
};
