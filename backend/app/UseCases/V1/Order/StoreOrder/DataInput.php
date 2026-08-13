<?php

namespace App\UseCases\V1\Order\StoreOrder;

/**
 * DTO входных данных создания заказа.
 */
final class DataInput
{
    public function __construct(
        public int $customerId,
        public int $warehouseId,
        public array $items,
    ) {}

    public static function create(array $data): self
    {
        return new self($data['customer_id'], $data['warehouse_id'], $data['items']);
    }

    /**
     * Нормализованный массив для сервиса остатков.
     */
    public function toServicePayload(): array
    {
        return [
            'customer_id'  => $this->customerId,
            'warehouse_id' => $this->warehouseId,
            'items'        => $this->items,
        ];
    }
}
