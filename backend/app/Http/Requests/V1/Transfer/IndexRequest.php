<?php

namespace App\Http\Requests\V1\Transfer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на список перемещений (фильтры и пагинация).
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
            'status'   => ['nullable', 'in:active,completed,canceled'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}