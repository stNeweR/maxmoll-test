<?php

namespace App\UseCases\V1\Transfer\StoreTransfer;

/**
 * DTO входных данных создания перемещения.
 */
final class DataInput
{
    /**
     * Конструктор DTO создания перемещения.
     *
     * @param int $fromWarehouseId Склад-источник.
     * @param int $toWarehouseId   Склад-получатель.
     * @param array $items         Список позиций перемещения.
     */
    public function __construct(public int $fromWarehouseId, public int $toWarehouseId, public array $items) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self($data['from_warehouse_id'], $data['to_warehouse_id'], $data['items']);
    }

    /**
     * Нормализованный массив для сервиса остатков.
     *
     * @return array
     */
    public function toServicePayload(): array
    {
        return [
            'from_warehouse_id' => $this->fromWarehouseId,
            'to_warehouse_id'   => $this->toWarehouseId,
            'items'             => $this->items,
        ];
    }
}
