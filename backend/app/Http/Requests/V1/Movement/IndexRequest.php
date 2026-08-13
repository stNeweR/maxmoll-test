<?php

namespace App\Http\Requests\V1\Movement;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на историю движений товаров (фильтры и пагинация).
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
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
            'product_id'   => ['nullable', 'integer', 'exists:products,id'],
            'doc_type'     => ['nullable', 'string', 'max:255'],
            'date_from'    => ['nullable', 'date'],
            'date_to'      => ['nullable', 'date'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}