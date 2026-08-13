<?php

namespace App\UseCases\V1\Warehouse\ListWarehouses;

/**
 * DTO результата списка складов.
 */
final class DataOutput
{
    /**
     * @param array $rows Строки результата.
     */
    public function __construct(private array $rows) {}

    /**
     * Сериализует результат в тело JSON-ответа.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['data' => $this->rows];
    }
}
