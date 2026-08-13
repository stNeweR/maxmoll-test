<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы поставок (supplies).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу supplies.
     */
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id()->comment('Идентификатор поставки');
            $table->unsignedBigInteger('warehouse_id')->comment('Склад, на который приходит поставка');
            $table->timestamps();

            // Внешние ключи
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};