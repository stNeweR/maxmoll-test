<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель позиции перемещения.
 */
#[Fillable(['transfer_id', 'product_id', 'count'])]
class TransferItem extends Model
{
    use HasFactory;

    /**
     * Перемещение, к которому относится позиция.
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    /**
     * Товар позиции.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}