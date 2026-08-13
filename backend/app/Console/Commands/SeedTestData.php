<?php

namespace App\Console\Commands;

use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\MovementRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\StockRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Команда наполнения справочников товаров, складов, остатков и клиентов
 * готовыми тестовыми данными.
 *
 * Запуск: php artisan ocrm:seed-test-data
 */
class SeedTestData extends Command
{
    /** Сигнатура команды. */
    protected $signature = 'ocrm:seed-test-data {--fresh : очистить таблицы перед наполнением}';

    /** Описание команды. */
    protected $description = 'Наполняет справочники товаров, складов, остатков и клиентов тестовыми данными';

    public function __construct(
        private WarehouseRepositoryInterface $warehouseRepository,
        private ProductRepositoryInterface $productRepository,
        private CustomerRepositoryInterface $customerRepository,
        private StockRepositoryInterface $stockRepository,
        private MovementRepositoryInterface $movementRepository,
    ) {
        parent::__construct();
    }

    /**
     * Выполняет наполнение тестовыми данными.
     */
    public function handle(): int
    {
        // По желанию очищаем данные перед наполнением.
        if ($this->option('fresh')) {
            $this->warn('Очистка таблиц...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Порядок важен из-за внешних ключей.
            foreach ([
                'movements', 'transfer_items', 'transfers',
                'supply_items', 'supplies', 'order_items', 'orders',
                'stocks', 'customers', 'products', 'warehouses',
            ] as $table) {
                DB::table($table)->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // --- Наполняем склады ---
        $warehouses = collect();
        foreach (['Основной склад', 'Второй склад', 'Склад в центре'] as $name) {
            $warehouses->push($this->warehouseRepository->create(['name' => $name]));
        }
        $this->info("Создано складов: {$warehouses->count()}");

        // --- Наполняем товары ---
        $products = collect();
        foreach ([
            'Ноутбук' => 59990.0,
            'Монитор' => 14990.0,
            'Клавиатура' => 2990.0,
            'Мышь' => 1290.0,
            'Наушники' => 4490.0,
            'USB-кабель' => 590.0,
            'Веб-камера' => 3990.0,
            'Микрофон' => 5490.0,
        ] as $name => $price) {
            $products->push($this->productRepository->create(['name' => $name, 'price' => $price]));
        }
        $this->info("Создано товаров: {$products->count()}");

        // --- Наполняем остатки по складам (случайные количества) ---
        $stockCount = 0;
        foreach ($warehouses as $wh) {
            // На первом складе остатки есть по всем товарам.
            $products->each(function (Product $p) use ($wh, &$stockCount) {
                $qty = random_int(10, 100);
                $this->stockRepository->insertOrIgnore($p->id, $wh->id, $qty);
                $stockCount++;

                // Первичное движение, фиксирующее стартовый остаток.
                $this->movementRepository->create([
                    'doc_type' => Product::class,
                    'doc_id' => $p->id,
                    'product_id' => $p->id,
                    'warehouse_id' => $wh->id,
                    'quantity' => $qty,
                    'created_at' => now(),
                ]);
            });
        }
        $this->info("Создано записей остатков: {$stockCount}");

        // --- Наполняем клиентов ---
        $names = ['Иван Петров', 'Мария Смирнова', 'Олег Иванов', 'Анна Кузнецова', 'Дмитрий Волков'];
        $customerCount = 0;
        foreach ($names as $name) {
            $this->customerRepository->create([
                'name' => $name,
                'phone' => '+7 900 '.Str::random(3).'-'.Str::random(2).'-'.Str::random(2),
                'email' => Str::slug($name).'@example.com',
            ]);
            $customerCount++;
        }
        $this->info("Создано клиентов: {$customerCount}");

        $this->info('Наполнение тестовыми данными завершено.');

        return self::SUCCESS;
    }
}
