<?php

namespace App\UseCases\V1\Customer\UpdateCustomer;

use Illuminate\Database\Eloquent\Model;

/**
 * DTO результата операции.
 */
final class DataOutput
{
    /**
     * @param Model $model Модель результата.
     */
    public function __construct(private Model $model) {}

    /**
     * Сериализует модель (с загруженными связями) в тело JSON-ответа.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['data' => $this->model->toArray()];
    }
}
