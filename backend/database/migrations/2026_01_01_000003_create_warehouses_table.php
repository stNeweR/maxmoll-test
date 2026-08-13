<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы складов (warehouses).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу warehouses.
     */
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id()->comment('Идентификатор склада');
            $table->string('name', 255)->comment('Название склада');
            $table->timestamps();
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};