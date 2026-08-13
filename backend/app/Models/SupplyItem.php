<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель позиции поставки.
 */
#[Fillable(['supply_id', 'product_id', 'count'])]
class SupplyItem extends Model
{
    use HasFactory;

    /**
     * Поставка, к которой относится позиция.
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    /**
     * Товар позиции.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}