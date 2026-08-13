<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель перемещения товаров между складами.
 *
 * Возможные статусы: active, completed, canceled.
 */
#[Fillable(['from_warehouse_id', 'to_warehouse_id', 'status', 'completed_at'])]
class Transfer extends Model
{
    use HasFactory;

    /** Статусы перемещения. */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    /** Кастуем временные поля к объектам Carbon. */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Склад-источник.
     */
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Склад-приёмник.
     */
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Позиции перемещения.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }
}