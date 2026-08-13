<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Модель движения товара (история изменения остатков).
 *
 * Каждая запись фиксирует изменение количества товара на остатке
 * склада и привязывается полиморфно к документу-источнику через
 * пару полей doc_type / doc_id (заказ, поставка, перемещение и т.п.).
 *
 * quantity — изменение количества: положительное (приход) или
 * отрицательное (расход) значение.
 */
#[Fillable(['doc_type', 'doc_id', 'product_id', 'warehouse_id', 'quantity'])]
class Movement extends Model
{
    use HasFactory;

    /** Кастуем временные поля к объектам Carbon. */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Полиморфная связь с документом-источником (Order, Supply, Transfer).
     */
    public function document(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Товар, по которому зафиксировано движение.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Склад, на остатке которого произошло изменение.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}