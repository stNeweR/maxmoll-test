<?php

namespace App\Http\Requests\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на список заказов (фильтры и пагинация).
 */
class IndexRequest extends FormRequest
{
    /**
     * Разрешено ли выполнять запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации параметров фильтрации и пагинации.
     */
    public function rules(): array
    {
        return [
            'status'       => ['nullable', 'in:active,completed,canceled'],
            'customer_id'  => ['nullable', 'integer', 'exists:customers,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}