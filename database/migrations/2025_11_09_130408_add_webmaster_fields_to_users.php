<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запустить миграцию.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Финансы
            $table->decimal('balance', 12, 2)->default(0)->after('status');
            $table->decimal('hold', 12, 2)->default(0)->after('balance');
            $table->string('payment_method', 20)->default('crypto')->after('hold');
            $table->string('payout_details', 255)->nullable()->after('payment_method');

            // Технические данные
            $table->string('api_token', 60)->unique()->nullable()->after('payout_details');
            $table->string('referral_code', 12)->unique()->nullable()->after('api_token');
            $table->ipAddress('registered_ip')->nullable()->after('referral_code');
            $table->string('user_agent', 512)->nullable()->after('referral_code');

            // Метрики
            $table->unsignedInteger('clicks_count')->default(0)->after('user_agent');
            $table->unsignedInteger('conversions_count')->default(0)->after('clicks_count');
            $table->unsignedInteger('rejections_count')->default(0)->after('conversions_count');
            $table->timestamp('last_activity_at')->nullable()->after('rejections_count');

            // Реферальная система
            $table->unsignedBigInteger('referrer_id')->nullable()->after('last_activity_at');
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('set null');
        });

        // Индексы (отдельно, чтобы не было ошибок при добавлении)
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('status');
            $table->index('api_token');
            $table->index('referral_code');
            $table->index('last_activity_at');
        });
    }

    /**
     * Откатить миграцию.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем внешний ключ
            $table->dropForeign(['referrer_id']);

            // Удаляем индексы
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['api_token']);
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['last_activity_at']);

            // Удаляем поля
            $table->dropColumn([
                'balance',
                'hold',
                'payment_method',
                'payout_details',
                'api_token',
                'referral_code',
                'registered_ip',
                'user_agent',
                'clicks_count',
                'conversions_count',
                'rejections_count',
                'last_activity_at',
                'referrer_id'
            ]);
        });
    }
};
