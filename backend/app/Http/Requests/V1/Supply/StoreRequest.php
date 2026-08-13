<?php

namespace App\Http\Requests\V1\Supply;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на создание поставки.
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
     * Правила валидации при создании поставки.
     */
    public function rules(): array
    {
        return [
            'warehouse_id'       => ['required', 'integer', 'exists:warehouses,id'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.count'      => ['required', 'integer', 'min:1'],
        ];
    }
}