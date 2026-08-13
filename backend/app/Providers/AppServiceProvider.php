<?php

namespace App\Providers;

use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\MovementRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\StockRepositoryInterface;
use App\Interfaces\SupplyRepositoryInterface;
use App\Interfaces\TransferRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\MovementRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StockRepository;
use App\Repositories\SupplyRepository;
use App\Repositories\TransferRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем репозитории: DI-контейнер подставляет реализацию
        // под каждый интерфейс доступа к данным.
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(MovementRepositoryInterface::class, MovementRepository::class);
        $this->app->bind(SupplyRepositoryInterface::class, SupplyRepository::class);
        $this->app->bind(TransferRepositoryInterface::class, TransferRepository::class);
        $this->app->bind(StockRepositoryInterface::class, StockRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
