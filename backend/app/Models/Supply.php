<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель поставки.
 */
#[Fillable(['warehouse_id'])]
class Supply extends Model
{
    use HasFactory;

    /**
     * Склад, на который приходит поставка.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Позиции поставки.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SupplyItem::class);
    }
}