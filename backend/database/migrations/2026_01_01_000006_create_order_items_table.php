<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы позиций заказа (order_items).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу order_items.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('Идентификатор позиции заказа');
            $table->unsignedBigInteger('order_id')->comment('Заказ, которому принадлежит позиция');
            $table->unsignedBigInteger('product_id')->comment('Товар в позиции');
            $table->integer('count')->comment('Количество товара в позиции');
            $table->timestamps();

            // Внешние ключи
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};