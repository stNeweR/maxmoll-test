<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы истории движений товаров (movements).
 *
 * Каждая запись фиксирует одно изменение количества товара на остатке склада.
 * Поля doc_type / doc_id связывают движение с документом-источником
 * (заказ, поставка, перемещение) при помощи полиморфной связи.
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу movements.
     */
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id()->comment('Идентификатор движения');
            $table->string('doc_type')->comment('Тип документа-источника (класс модели)');
            $table->unsignedBigInteger('doc_id')->comment('Идентификатор документа-источника');
            $table->unsignedBigInteger('product_id')->comment('Товар, по которому зафиксировано движение');
            $table->unsignedBigInteger('warehouse_id')->comment('Склад, на остатке которого произошло изменение');
            $table->integer('quantity')->comment('Изменение количества на остатке (+ / -)');
            $table->timestamps();

            // Индексы для быстрой фильтрации
            $table->index(['doc_type', 'doc_id']);
            $table->index(['product_id', 'warehouse_id']);
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};