<?php

namespace App\Http\Requests\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Запрос на создание клиента.
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
     * Правила валидации при создании клиента.
     */
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255', Rule::unique('customers', 'phone')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')],
        ];
    }
}