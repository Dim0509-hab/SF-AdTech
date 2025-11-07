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
    Schema::table('offer_webmaster', function (Blueprint $table) {
        $table->decimal('cost_per_click', 8, 2)
              ->nullable()
              ->after('offer_id');
    });
}

public function down()
{
    Schema::table('offer_webmaster', function (Blueprint $table) {
        $table->dropColumn('cost_per_click');
    });
}

};
