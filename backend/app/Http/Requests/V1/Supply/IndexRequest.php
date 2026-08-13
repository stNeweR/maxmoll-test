<?php

namespace App\Http\Requests\V1\Supply;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на список поставок (фильтры и пагинация).
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
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}