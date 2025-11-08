<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запускаем миграцию
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Добавляем поле status после роли
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('role')
                  ->comment('Статус модерации: ожидает, одобрен, отклонён');
        });
    }

    /**
     * Откатываем миграцию
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
