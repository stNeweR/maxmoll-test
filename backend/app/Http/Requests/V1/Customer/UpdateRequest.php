<?php

namespace App\Http\Requests\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Запрос на обновление клиента.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Разрешено ли выполнять запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации при обновлении клиента.
     *
     * Исключаем текущего клиента из проверки уникальности e-mail/телефона.
     */
    public function rules(): array
    {
        $id = $this->route('customer') ? (int) $this->route('customer') : null;

        return [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255', Rule::unique('customers', 'phone')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($id)],
        ];
    }
}