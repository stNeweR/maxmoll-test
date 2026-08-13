<?php

namespace App\Repositories;

use App\Exceptions\BusinessException;
use App\Interfaces\StockRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Репозиторий остатков.
 *
 * Реализует доступ к данным таблицы stocks.
 */
class StockRepository implements StockRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function available(int $productId, int $warehouseId, bool $lock = false): int
    {
        $query = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);
        if ($lock) {
            $query->lockForUpdate();
        }

        return (int) $query->value('stock');
    }

    /**
     * {@inheritDoc}
     */
    public function change(int $productId, int $warehouseId, int $delta): int
    {
        // Берём строку остатка под блокировку, чтобы избежать пересечения операций.
        $current = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->value('stock');

        $newValue = max(0, (int) $current) + $delta;

        // Новое значение остатка не может уходить в минус.
        if ($newValue < 0) {
            throw new BusinessException(
                "Недостаточно товара (id={$productId}) на складе (id={$warehouseId}). Доступно: {$current}."
            );
        }

        // Обновляем или создаём строку остатка (таблица имеет составной ключ).
        $exists = DB::table('stocks')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->exists();

        if ($exists) {
            DB::table('stocks')
                ->where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->update(['stock' => $newValue]);
        } else {
            DB::table('stocks')->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'stock' => $newValue,
            ]);
        }

        return $newValue;
    }

    /**
     * {@inheritDoc}
     */
    public function insertOrIgnore(int $productId, int $warehouseId, int $stock): void
    {
        DB::table('stocks')->insertOrIgnore([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'stock' => $stock,
        ]);
    }
}
