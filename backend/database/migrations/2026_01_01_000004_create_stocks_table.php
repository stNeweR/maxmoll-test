<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы остатков (stocks).
 *
 * Таблица не имеет собственного поле id — первичный ключ составной
 * (product_id, warehouse_id), что гарантирует уникальность остатка
 * по паре «товар + склад».
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу stocks.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->comment('Товар');
            $table->unsignedBigInteger('warehouse_id')->comment('Склад');
            $table->integer('stock')->comment('Количество товара на остатке');
            $table->timestamps();

            // Составной первичный ключ
            $table->primary(['product_id', 'warehouse_id']);

            // Внешние ключи
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};