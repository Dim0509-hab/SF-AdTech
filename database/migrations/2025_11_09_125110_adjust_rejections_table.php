<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Запустить миграцию.
     */
    public function up(): void
    {
        // 1. Меняем тип reason с TEXT на VARCHAR(50) и добавляем индекс
        if (Schema::hasColumn('rejections', 'reason')) {
            DB::statement('ALTER TABLE rejections MODIFY reason VARCHAR(50) NULL');
        }
        Schema::table('rejections', function (Blueprint $table) {
            $table->index('reason', 'idx_rejections_reason');
        });

        // 2. Добавляем новые поля, если их ещё нет
        Schema::table('rejections', function (Blueprint $table) {
            if (!Schema::hasColumn('rejections', 'ip')) {
                $table->string('ip', 45)->nullable()->after('context');
            }
            if (!Schema::hasColumn('rejections', 'user_agent')) {
                $table->string('user_agent', 512)->nullable()->after('ip');
            }
            if (!Schema::hasColumn('rejections', 'referer')) {
                $table->string('referer', 1024)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('rejections', 'click_token')) {
                $table->string('click_token', 32)->nullable()->after('referer');
            }
            if (!Schema::hasColumn('rejections', 'is_suspicious')) {
                $table->boolean('is_suspicious')->default(false)->after('context');
            }
        });

        // 3. Добавляем индексы для производительности
        Schema::table('rejections', function (Blueprint $table) {
            // Индекс по IP + время — для поиска накрутки
            $table->index(['ip', 'created_at'], 'idx_rejections_ip_created');

            // Индекс по подозрительным записям
            $table->index('is_suspicious', 'idx_rejections_suspicious');

            // Индекс по link_hash — для связи с ссылкой
            $table->index('link_hash', 'idx_rejections_link_hash');

            // Индекс по времени — для аналитики
            $table->index('created_at', 'idx_rejections_created_at');
        });
    }

    /**
     * Откатить миграцию.
     */
    public function down(): void
    {
        Schema::table('rejections', function (Blueprint $table) {
            // Удаляем индексы
            $table->dropIndex('idx_rejections_reason');
            $table->dropIndex('idx_rejections_ip_created');
            $table->dropIndex('idx_rejections_suspicious');
            $table->dropIndex('idx_rejections_link_hash');
            $table->dropIndex('idx_rejections_created_at');

            // Удаляем новые поля
            $table->dropColumn(['ip', 'user_agent', 'referer', 'click_token', 'is_suspicious']);
        });

        // Возвращаем reason в TEXT
        DB::statement('ALTER TABLE rejections MODIFY reason TEXT NULL');
    }
};
