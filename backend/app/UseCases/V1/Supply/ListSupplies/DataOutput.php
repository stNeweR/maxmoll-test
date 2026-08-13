<?php

namespace App\UseCases\V1\Supply\ListSupplies;

/**
 * DTO результата операции (список данных + метаинформация пагинации).
 */
final class DataOutput
{
    public function __construct(private array $rows, private array $meta) {}

    /**
     * Строки результата.
     */
    public function rows(): array
    {
        return $this->rows;
    }

    /**
     * Метаинформация пагинации.
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * Сериализует результат в тело JSON-ответа.
     */
    public function toArray(): array
    {
        return ['data' => $this->rows, 'meta' => $this->meta];
    }
}
