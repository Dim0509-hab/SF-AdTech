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
    Schema::table('offers', function (Blueprint $table) {
        $table->index('advertiser_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('offers', function (Blueprint $table) {
        $table->dropIndex(['advertiser_id']); // или $table->dropIndex('offers_advertiser_id');
    });
}

};
