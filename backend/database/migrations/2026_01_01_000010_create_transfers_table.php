<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы перемещений между складами (transfers).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу transfers.
     */
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id()->comment('Идентификатор перемещения');
            $table->unsignedBigInteger('from_warehouse_id')->comment('Склад-источник, откуда перемещается товар');
            $table->unsignedBigInteger('to_warehouse_id')->comment('Склад-приёмник, куда перемещается товар');
            $table->timestamps();
            $table->timestamp('completed_at')->nullable()->comment('Время проведения перемещения');
            $table->string('status', 255)->comment('Статус перемещения: active / completed / canceled');

            // Внешние ключи
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};