<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель заказа.
 *
 * Возможные статусы: active, completed, canceled.
 */
#[Fillable(['customer_id', 'warehouse_id', 'status', 'completed_at'])]
class Order extends Model
{
    use HasFactory;

    /** Статусы заказа. */
    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';

    /** Кастуем временные поля к объектам Carbon. */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Клиент, на которого оформлен заказ.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Склад отгрузки заказа.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Позиции заказа.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Заказ активен (в работе)?
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Заказ выполнен?
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Заказ отменён?
     */
    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }
}