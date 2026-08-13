<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Миграция создания таблицы клиентов (customers).
 */
return new class extends Migration
{
    /**
     * Создаёт таблицу customers.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id()->comment('Идентификатор клиента');
            $table->string('name', 255)->comment('Имя клиента');
            $table->string('phone', 255)->nullable()->comment('Телефон клиента (может отсутствовать)');
            $table->string('email', 255)->nullable()->comment('E-mail клиента (может отсутствовать)');
            $table->timestamps();
        });
    }

    /**
     * Откатывает миграцию.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};