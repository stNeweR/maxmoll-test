<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы позиций поставки (supply_items).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу supply_items.
     */
    public function up(): void
    {
        Schema::create('supply_items', function (Blueprint $table) {
            $table->id()->comment('Идентификатор позиции поставки');
            $table->unsignedBigInteger('supply_id')->comment('Поставка, которой принадлежит позиция');
            $table->unsignedBigInteger('product_id')->comment('Товар в позиции');
            $table->integer('count')->comment('Количество товара в позиции');
            $table->timestamps();

            // Внешние ключи
            $table->foreign('supply_id')->references('id')->on('supplies')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_items');
    }
};