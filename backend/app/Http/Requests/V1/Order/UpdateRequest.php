<?php

namespace App\Http\Requests\V1\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Запрос на обновление заказа (клиент, склад и состав позиций).
 *
 * Статус заказа в рамках этого запроса не меняется — смена статуса
 * выполняется отдельными маршрутами (complete/cancel/revive).
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
     * Правила валидации при обновлении заказа.
     */
    public function rules(): array
    {
        return [
            'customer_id'        => ['required', 'integer', 'exists:customers,id'],
            'warehouse_id'       => ['required', 'integer', 'exists:warehouses,id'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.count'      => ['required', 'integer', 'min:1'],
        ];
    }
}