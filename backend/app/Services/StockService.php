<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Interfaces\MovementRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\StockRepositoryInterface;
use App\Interfaces\SupplyRepositoryInterface;
use App\Interfaces\TransferRepositoryInterface;
use App\Models\Movement;
use App\Models\Order;
use App\Models\Supply;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

/**
 * Сервис управления остатками товаров и движениями.
 *
 * Является единственным местом, где корректно изменяются остатки
 * и фиксируется история движений. Все операции, влияющие на остатки
 * (заказы, поставки, перемещения), выполняются здесь.
 *
 * Модель работы с заказами:
 *  - active    — товары зарезервированы (списаны с остатка как «в работе»);
 *  - completed — заказ выполнен, товары окончательно проданы;
 *  - canceled  — товары возвращены на остаток.
 *
 * Доступ к данным выполняется исключительно через репозитории.
 */
class StockService
{
    /**
     * Инъекция зависимостей сервиса.
     *
     * @param  OrderRepositoryInterface  $orderRepository  репозиторий заказов
     * @param  SupplyRepositoryInterface  $supplyRepository  репозиторий поставок
     * @param  TransferRepositoryInterface  $transferRepository  репозиторий перемещений
     * @param  StockRepositoryInterface  $stockRepository  репозиторий остатков
     * @param  MovementRepositoryInterface  $movementRepository  репозиторий движений
     * @param  ProductRepositoryInterface  $productRepository  репозиторий товаров
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private SupplyRepositoryInterface $supplyRepository,
        private TransferRepositoryInterface $transferRepository,
        private StockRepositoryInterface $stockRepository,
        private MovementRepositoryInterface $movementRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Текущий доступный остаток товара на складе.
     *
     * @param  int  $productId  id товара
     * @param  int  $warehouseId  id склада
     * @param  bool  $lock  заблокировать строку (для транзакций)
     * @return int количество доступного товара
     */
    public function available(int $productId, int $warehouseId, bool $lock = false): int
    {
        return $this->stockRepository->available($productId, $warehouseId, $lock);
    }

    /**
     * Изменяет остаток товара на складе и фиксирует движение.
     *
     * @param  int  $delta  изменение количества (может быть отрицательным)
     * @return Movement созданная запись движения
     */
    public function changeStock(int $productId, int $warehouseId, int $delta, object $document): Movement
    {
        // Обновляем остаток (проверка доступности и блокировка внутри репозитория).
        $this->stockRepository->change($productId, $warehouseId, $delta);

        // Фиксируем движение в истории.
        return $this->movementRepository->create([
            'doc_type' => get_class($document),
            'doc_id' => $document->getKey(),
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity' => $delta,
        ]);
    }

    /**
     * Бронирует (резервирует) товары по позициям — списывает с доступного остатка.
     */
    private function reserve(array $items, int $warehouseId, object $document): void
    {
        foreach ($items as $item) {
            $this->changeStock((int) $item['product_id'], $warehouseId, -((int) $item['count']), $document);
        }
    }

    /**
     * Возвращает товары на остаток по позициям.
     */
    private function release(array $items, int $warehouseId, object $document): void
    {
        foreach ($items as $item) {
            $this->changeStock((int) $item['product_id'], $warehouseId, (int) $item['count'], $document);
        }
    }

    /**
     * Валидирует набор позиций (товары должны существовать, количества > 0).
     */
    private function normalizeItems(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $count = (int) ($item['count'] ?? 0);
            if ($productId <= 0) {
                throw new BusinessException('Указан некорректный id товара в позиции.');
            }
            if ($count <= 0) {
                throw new BusinessException('Количество в позиции должно быть больше нуля.');
            }
            if (! $this->productRepository->exists($productId)) {
                throw new BusinessException("Товар с id {$productId} не найден.");
            }
            $normalized[] = ['product_id' => $productId, 'count' => $count];
        }
        if (empty($normalized)) {
            throw new BusinessException('Заказ или поставка должны содержать хотя бы одну позицию.');
        }

        return $normalized;
    }

    /**
     * Преобразует коллекцию Eloquent-позиций в массив ['product_id' => ..., 'count' => ...].
     */
    private function itemsToArray($items): array
    {
        return $items->map(function ($item) {
            return ['product_id' => (int) $item->product_id, 'count' => (int) $item->count];
        })->values()->all();
    }

    /**
     * Создаёт заказ с позициями и резервирует товары.
     *
     * @return Order созданный заказ
     */
    public function createOrder(array $data): Order
    {
        $items = $this->normalizeItems($data['items']);

        return DB::transaction(function () use ($data, $items) {
            $order = $this->orderRepository->createWithItems([
                'customer_id' => (int) $data['customer_id'],
                'warehouse_id' => (int) $data['warehouse_id'],
                'status' => Order::STATUS_ACTIVE,
                'completed_at' => null,
            ], $items);

            // Резервируем товары (создаются записи движений).
            $this->reserve($items, $order->warehouse_id, $order);

            return $order;
        });
    }

    /**
     * Обновляет заказ: меняет клиента, склад и состав позиций (не статус).
     *
     * Обновлять можно только активный заказ (выполненный/отменённый нельзя).
     *
     * @return Order обновлённый заказ
     */
    public function updateOrder(Order $order, array $data): Order
    {
        // Выполненный и отменённый заказ менять нельзя.
        if ($order->isCompleted()) {
            throw new BusinessException('Нельзя обновлять выполненный заказ.');
        }
        if ($order->isCanceled()) {
            throw new BusinessException('Нельзя обновлять отменённый заказ.');
        }

        $newItems = $this->normalizeItems($data['items']);

        return DB::transaction(function () use ($order, $newItems, $data) {
            // Возвращаем старый состав на остаток, затем бронируем новый.
            $oldItems = $this->itemsToArray($order->items);
            $this->release($oldItems, $order->warehouse_id, $order);
            $this->reserve($newItems, (int) $data['warehouse_id'], $order);

            // Актуализируем данные заказа.
            $this->orderRepository->updateAttributes($order, [
                'customer_id' => (int) $data['customer_id'],
                'warehouse_id' => (int) $data['warehouse_id'],
            ]);

            // Пересоздаём позиции заказа.
            $this->orderRepository->syncItems($order, $newItems);

            return $this->orderRepository->findWithRelations($order->getKey());
        });
    }

    /**
     * Завершает заказ: меняет статус на completed. (Остатки уже зарезервированы.)
     */
    public function completeOrder(Order $order): void
    {
        if (! $order->isActive()) {
            throw new BusinessException('Завершить можно только заказ в работе.');
        }
        $this->orderRepository->updateStatus($order, Order::STATUS_COMPLETED, now());
    }

    /**
     * Отменяет заказ: возвращает зарезервированные товары на остаток.
     */
    public function cancelOrder(Order $order): void
    {
        if ($order->isCanceled()) {
            throw new BusinessException('Заказ уже отменён.');
        }
        if ($order->isCompleted()) {
            throw new BusinessException('Нельзя отменить выполненный заказ.');
        }

        DB::transaction(function () use ($order) {
            $items = $this->itemsToArray($order->items);
            // Возвращаем товары на остаток, фиксируя движения.
            $this->release($items, $order->warehouse_id, $order);

            $this->orderRepository->updateStatus($order, Order::STATUS_CANCELED, null);
        });
    }

    /**
     * Возобновляет отменённый заказ (перевод в работу).
     *
     * Перед повторной резервацией проверяется наличие товаров.
     */
    public function reviveOrder(Order $order): void
    {
        if (! $order->isCanceled()) {
            throw new BusinessException('Возобновить можно только отменённый заказ.');
        }

        DB::transaction(function () use ($order) {
            $items = $this->itemsToArray($order->items);
            // Проверка доступности и резервация в одной транзакции.
            $this->reserve($items, $order->warehouse_id, $order);

            $this->orderRepository->updateStatus($order, Order::STATUS_ACTIVE, null);
        });
    }

    /**
     * Создаёт поставку на склад и увеличивает остатки.
     *
     * @return Supply созданная поставка
     */
    public function createSupply(array $data): Supply
    {
        $items = $this->normalizeItems($data['items']);

        return DB::transaction(function () use ($data, $items) {
            $supply = $this->supplyRepository->createWithItems([
                'warehouse_id' => (int) $data['warehouse_id'],
            ], $items);

            foreach ($items as $item) {
                // Увеличиваем остаток (создаётся запись движения).
                $this->changeStock($item['product_id'], $supply->warehouse_id, $item['count'], $supply);
            }

            return $supply;
        });
    }

    /**
     * Создаёт перемещение между складами (пока не влияет на остатки).
     *
     * @return Transfer созданное перемещение
     */
    public function createTransfer(array $data): Transfer
    {
        $from = (int) $data['from_warehouse_id'];
        $to = (int) $data['to_warehouse_id'];
        if ($from === $to) {
            throw new BusinessException('Склад-источник и склад-приёмник не могут совпадать.');
        }
        $items = $this->normalizeItems($data['items']);

        return DB::transaction(function () use ($from, $to, $items) {
            return $this->transferRepository->createWithItems([
                'from_warehouse_id' => $from,
                'to_warehouse_id' => $to,
                'status' => Transfer::STATUS_ACTIVE,
                'completed_at' => null,
            ], $items);
        });
    }

    /**
     * Проводит перемещение: списывает со склада-источника и оприходывает на склад-приёмник.
     */
    public function completeTransfer(Transfer $transfer): void
    {
        if ($transfer->status !== Transfer::STATUS_ACTIVE) {
            throw new BusinessException('Провести можно только активное перемещение.');
        }

        DB::transaction(function () use ($transfer) {
            $items = $this->itemsToArray($transfer->items);

            // Списываем товары со склада-источника.
            foreach ($items as $item) {
                $this->changeStock($item['product_id'], $transfer->from_warehouse_id, -$item['count'], $transfer);
            }
            // Оприходываем товары на склад-приёмник.
            foreach ($items as $item) {
                $this->changeStock($item['product_id'], $transfer->to_warehouse_id, $item['count'], $transfer);
            }

            $this->transferRepository->updateStatus($transfer, Transfer::STATUS_COMPLETED, now());
        });
    }

    /**
     * Отменяет перемещение (остатки не менялись, поэтому просто меняем статус).
     */
    public function cancelTransfer(Transfer $transfer): void
    {
        if ($transfer->status !== Transfer::STATUS_ACTIVE) {
            throw new BusinessException('Отменить можно только активное перемещение.');
        }
        $this->transferRepository->updateStatus($transfer, Transfer::STATUS_CANCELED, null);
    }
}
