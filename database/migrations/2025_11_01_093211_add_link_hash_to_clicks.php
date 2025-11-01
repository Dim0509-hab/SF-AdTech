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
        $table->string('link_hash')->nullable()->after('offer_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('clicks', function (Blueprint $table) {
        $table->dropColumn('link_hash');
    });
}

};
