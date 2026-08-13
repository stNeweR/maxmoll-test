<?php

namespace App\Http\Requests\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на список клиентов (фильтры и пагинация).
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
            'search'   => ['nullable', 'string', 'max:255'],
            'email'    => ['nullable', 'email'],
            'phone'    => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}