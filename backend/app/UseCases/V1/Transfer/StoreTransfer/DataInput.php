<?php

namespace App\UseCases\V1\Transfer\StoreTransfer;

/**
 * DTO входных данных создания перемещения.
 */
final class DataInput
{
    public function __construct(public int $fromWarehouseId, public int $toWarehouseId, public array $items) {}

    public static function create(array $data): self
    {
        return new self($data['from_warehouse_id'], $data['to_warehouse_id'], $data['items']);
    }

    public function toServicePayload(): array
    {
        return [
            'from_warehouse_id' => $this->fromWarehouseId,
            'to_warehouse_id'   => $this->toWarehouseId,
            'items'             => $this->items,
        ];
    }
}
