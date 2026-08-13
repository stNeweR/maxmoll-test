<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Модель клиента.
 */
#[Fillable(['name', 'phone', 'email'])]
class Customer extends Model
{
    use HasFactory;

    /**
     * Заказы клиента.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}