<?php

namespace App\UseCases\V1\Order\UpdateOrder;

/**
 * DTO входных данных обновления заказа.
 */
final class DataInput
{
    public function __construct(
        public int $id,
        public int $customerId,
        public int $warehouseId,
        public array $items,
    ) {}

    public static function create(array $data, int $id): self
    {
        return new self($id, $data['customer_id'], $data['warehouse_id'], $data['items']);
    }

    public function toServicePayload(): array
    {
        return [
            'customer_id'  => $this->customerId,
            'warehouse_id' => $this->warehouseId,
            'items'        => $this->items,
        ];
    }
}
