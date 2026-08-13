<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы товаров (products).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу products.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('Идентификатор товара');
            $table->string('name', 255)->comment('Название товара');
            $table->float('price')->comment('Цена товара');
            $table->timestamps();
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};