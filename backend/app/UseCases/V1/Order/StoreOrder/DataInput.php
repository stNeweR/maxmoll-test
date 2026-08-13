<?php

namespace App\UseCases\V1\Order\StoreOrder;

/**
 * DTO входных данных создания заказа.
 */
final class DataInput
{
    /**
     * Конструктор DTO создания заказа.
     *
     * @param int $customerId  Идентификатор клиента.
     * @param int $warehouseId Идентификатор склада.
     * @param array $items     Список позиций заказа.
     */
    public function __construct(
        public int $customerId,
        public int $warehouseId,
        public array $items,
    ) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @return self
     */
    public static function create(array $data): self
    {
        return new self($data['customer_id'], $data['warehouse_id'], $data['items']);
    }

    /**
     * Нормализованный массив для сервиса остатков.
     *
     * @return array
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
