<?php

namespace App\Http\Requests\V1\Transfer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на создание перемещения между складами.
 */
class StoreRequest extends FormRequest
{
    /**
     * Разрешено ли выполнять запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации при создании перемещения.
     */
    public function rules(): array
    {
        return [
            'from_warehouse_id'  => ['required', 'integer', 'exists:warehouses,id', 'different:to_warehouse_id'],
            'to_warehouse_id'    => ['required', 'integer', 'exists:warehouses,id'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.count'      => ['required', 'integer', 'min:1'],
        ];
    }
}