<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель остатка товара на складе.
 *
 * Таблица имеет составной первичный ключ (product_id, warehouse_id)
 * и не содержит поля id. Модель используется только для чтения;
 * запись остатков выполняется напрямую в StockService.
 */
#[Fillable(['product_id', 'warehouse_id', 'stock'])]
class Stock extends Model
{
    use HasFactory;

    /**
     * Товар, к которому относится остаток.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Склад, к которому относится остаток.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}