<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->index('ip');
            $table->index('country');
            $table->index('device_type');
            $table->index('webmaster_id');
            $table->index('offer_id');
            $table->index('redirected_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropIndex(['ip', 'country', 'device_type', 'webmaster_id', 'offer_id', 'redirected_at', 'created_at']);
        });
    }
};
