<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запускает миграцию.
     */
    public function up(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            // Широта и долгота
            if (!Schema::hasColumn('clicks', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('city');
            }
            if (!Schema::hasColumn('clicks', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }

            // Почтовый индекс (опционально)
            if (!Schema::hasColumn('clicks', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('city');
            }
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'postal_code']);
        });
    }
};
