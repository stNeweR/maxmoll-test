<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель товара.
 */
#[Fillable(['name', 'price'])]
class Product extends Model
{
    use HasFactory;

    /**
     * Позиции заказов, содержащие этот товар.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Записи остатков по складам для этого товара.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }
}