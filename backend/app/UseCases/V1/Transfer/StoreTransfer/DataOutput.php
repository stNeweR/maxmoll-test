<?php

namespace App\UseCases\V1\Transfer\StoreTransfer;

use Illuminate\Database\Eloquent\Model;

/**
 * DTO результата операции.
 */
final class DataOutput
{
    public function __construct(private Model $model) {}

    /**
     * Сериализует модель (с загруженными связями) в тело JSON-ответа.
     */
    public function toArray(): array
    {
        return ['data' => $this->model->toArray()];
    }
}
