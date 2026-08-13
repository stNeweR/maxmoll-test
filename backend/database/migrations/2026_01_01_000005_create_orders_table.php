<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы заказов (orders).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу orders.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Идентификатор заказа');
            $table->unsignedBigInteger('customer_id')->comment('Клиент, которому принадлежит заказ');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable()->comment('Время выполнения заказа');
            $table->unsignedBigInteger('warehouse_id')->comment('Склад отгрузки');
            $table->string('status', 255)->comment('Статус заказа: active / completed / canceled');

            // Внешние ключи
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};