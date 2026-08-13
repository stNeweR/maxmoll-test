<?php

namespace App\UseCases\V1\Order\UpdateOrder;

/**
 * DTO входных данных обновления заказа.
 */
final class DataInput
{
    /**
     * Конструктор DTO обновления заказа.
     *
     * @param int $id          Идентификатор заказа.
     * @param int $customerId  Идентификатор клиента.
     * @param int $warehouseId Идентификатор склада.
     * @param array $items     Список позиций заказа.
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public int $warehouseId,
        public array $items,
    ) {}

    /**
     * Создаёт DTO из массива входных данных.
     *
     * @param array $data Массив входных данных.
     * @param int $id     Идентификатор заказа.
     * @return self
     */
    public static function create(array $data, int $id): self
    {
        return new self($id, $data['customer_id'], $data['warehouse_id'], $data['items']);
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
