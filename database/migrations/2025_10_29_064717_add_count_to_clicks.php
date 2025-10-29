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
    Schema::table('clicks', function (Blueprint $table) {
        $table->unsignedInteger('count')->default(1); // или ->nullable()
        $table->index('count');
    });
}

public function down()
{
    Schema::table('clicks', function (Blueprint $table) {
        $table->dropColumn('count');
    });
}

};
