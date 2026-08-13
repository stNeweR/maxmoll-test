<?php

namespace App\UseCases\V1\Supply\StoreSupply;

/**
 * DTO входных данных создания поставки.
 */
final class DataInput
{
    /**
     * Конструктор DTO создания поставки.
     *
     * @param int $warehouseId Идентификатор склада.
     * @param array $items     Список позиций поставки.
     */
    public function __construct(public int $warehouseId, public array $items) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self($data['warehouse_id'], $data['items']);
    }

    /**
     * Нормализованный массив для сервиса остатков.
     *
     * @return array
     */
    public function toServicePayload(): array
    {
        return ['warehouse_id' => $this->warehouseId, 'items' => $this->items];
    }
}
