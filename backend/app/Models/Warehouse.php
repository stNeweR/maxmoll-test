<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель склада.
 */
#[Fillable(['name'])]
class Warehouse extends Model
{
    use HasFactory;

    /**
     * Остатки на этом складе.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }
}