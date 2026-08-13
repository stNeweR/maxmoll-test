<?php

namespace App\UseCases\V1\Supply\ListSupplies;

/**
 * DTO результата операции (список данных + метаинформация пагинации).
 */
final class DataOutput
{
    /**
     * @param array $rows Строки результата.
     * @param array $meta Метаинформация пагинации.
     */
    public function __construct(private array $rows, private array $meta) {}

    /**
     * Строки результата.
     *
     * @return array
     */
    public function rows(): array
    {
        return $this->rows;
    }

    /**
     * Метаинформация пагинации.
     *
     * @return array
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * Сериализует результат в тело JSON-ответа.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['data' => $this->rows, 'meta' => $this->meta];
    }
}
