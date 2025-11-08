<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции: добавление недостающих полей и индексов
     */
    public function up(): void
    {
        // Проверяем, существует ли таблица
        if (!Schema::hasTable('offer_webmaster')) {
            $this->createTable();
            return;
        }

        // Добавляем поля, если их нет
        Schema::table('offer_webmaster', function (Blueprint $table) {
            if (!Schema::hasColumn('offer_webmaster', 'cost_per_click')) {
                $table->decimal('cost_per_click', 10, 2)->default(0.00)->after('offer_id');
            }

            if (!Schema::hasColumn('offer_webmaster', 'agreed_price')) {
                $table->decimal('agreed_price', 10, 2)->default(0.00)->after('cost_per_click');
            }

            if (!Schema::hasColumn('offer_webmaster', 'status')) {
                $table->enum('status', ['active', 'paused', 'rejected'])->default('active')->after('agreed_price');
            }

            // Добавляем timestamps, если их нет
            if (!Schema::hasColumn('offer_webmaster', 'created_at')) {
                $table->timestamps();
            } elseif (!Schema::hasColumn('offer_webmaster', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Проверяем первичный ключ
        $this->fixPrimaryKey();
    }

    /**
     * Создание таблицы с нуля (если не существует)
     */
    private function createTable(): void
    {
        Schema::create('offer_webmaster', function (Blueprint $table) {
            $table->foreignId('webmaster_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');

            $table->decimal('cost_per_click', 10, 2)->default(0.00);
            $table->decimal('agreed_price', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'paused', 'rejected'])->default('active');

            $table->timestamps();

            // Составной первичный ключ
            $table->primary(['webmaster_id', 'offer_id']);
        });
    }

    /**
     * Исправление первичного ключа, если он не задан
     */
    private function fixPrimaryKey(): void
    {
        $currentKeys = Schema::getConnection()
            ->selectOne("SHOW INDEX FROM offer_webmaster WHERE Key_name = 'PRIMARY'");

        if (!$currentKeys) {
            // Удаляем возможные дублирующие индексы
            if (Schema::hasIndex('offer_webmaster', 'offer_webmaster_webmaster_id_offer_id_index')) {
                Schema::table('offer_webmaster', function (Blueprint $table) {
                    $table->dropIndex('offer_webmaster_webmaster_id_offer_id_index');
                });
            }

            // Устанавливаем составной первичный ключ
            Schema::table('offer_webmaster', function (Blueprint $table) {
                $table->primary(['webmaster_id', 'offer_id']);
            });
        }
    }

    /**
     * Откат миграции (только для разработки!)
     */
    public function down(): void
    {
        // ⚠️ Осторожно: удаление таблицы
        Schema::dropIfExists('offer_webmaster');
    }
};
