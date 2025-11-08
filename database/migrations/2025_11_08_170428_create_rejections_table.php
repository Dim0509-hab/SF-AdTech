<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rejections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webmaster_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->string('link_hash')->nullable(); // уникальный хеш ссылки
            $table->text('reason')->nullable();     // причина отказа
            $table->json('context')->nullable();    // дополнительные данные (IP, UA и т.п.)
            $table->timestamps();

            // Индекс для ускорения запросов
            $table->index('link_hash');
            $table->index(['webmaster_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rejections');
    }
};
