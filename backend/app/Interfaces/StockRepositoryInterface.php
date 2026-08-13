<?php

namespace App\Interfaces;

/**
 * Интерфейс репозитория остатков.
 *
 * Описывает операции доступа к данным таблицы stocks.
 */
interface StockRepositoryInterface
{
    /**
     * Возвращает текущий доступный остаток товара на складе.
     *
     * @param  bool  $lock  заблокировать строку (для транзакций)
     */
    public function available(int $productId, int $warehouseId, bool $lock = false): int;

    /**
     * Изменяет остаток товара на складе на указанную величину.
     *
     * Строка остатка берётся под блокировку, уход в минус запрещён.
     * Возвращает новое значение остатка.
     */
    public function change(int $productId, int $warehouseId, int $delta): int;

    /**
     * Вставляет строку остатка, если её ещё нет (для наполнения тестовыми данными).
     */
    public function insertOrIgnore(int $productId, int $warehouseId, int $stock): void;
}
