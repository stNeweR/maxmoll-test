<?php

namespace App\UseCases\V1\Supply\ListSupplies;

/**
 * DTO входных данных списка поставок.
 */
final class DataInput
{
    /**
     * Конструктор DTO списка поставок.
     *
     * @param ?int $warehouseId Идентификатор склада.
     * @param int $perPage      Количество записей на страницу.
     */
    public function __construct(public ?int $warehouseId, public int $perPage) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            isset($data['warehouse_id']) ? (int) $data['warehouse_id'] : null,
            max(1, min((int) ($data['per_page'] ?? 15), 100)),
        );
    }
}
