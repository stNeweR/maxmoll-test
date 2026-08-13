<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы позиций перемещения (transfer_items).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу transfer_items.
     */
    public function up(): void
    {
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id()->comment('Идентификатор позиции перемещения');
            $table->unsignedBigInteger('transfer_id')->comment('Перемещение, которому принадлежит позиция');
            $table->unsignedBigInteger('product_id')->comment('Товар в позиции');
            $table->integer('count')->comment('Количество перемещаемого товара');
            $table->timestamps();

            // Внешние ключи
            $table->foreign('transfer_id')->references('id')->on('transfers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
    }
};