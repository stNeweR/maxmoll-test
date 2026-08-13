<?php

namespace App\UseCases\V1\Supply\StoreSupply;

/**
 * DTO входных данных создания поставки.
 */
final class DataInput
{
    public function __construct(public int $warehouseId, public array $items) {}

    public static function create(array $data): self
    {
        return new self($data['warehouse_id'], $data['items']);
    }

    public function toServicePayload(): array
    {
        return ['warehouse_id' => $this->warehouseId, 'items' => $this->items];
    }
}
