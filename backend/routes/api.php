<?php

use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\MovementController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SupplyController;
use App\Http\Controllers\Api\V1\TransferController;
use App\Http\Controllers\Api\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

/**
 * REST-маршруты микро-CRM.
 *
 * Версионирование выполнено через группу маршрутов с префиксом v1,
 * поэтому все эндпоинты доступны по адресу /api/v1/... .
 */
Route::prefix('v1')->group(function () {
    // Справочники складов и товаров.
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);

    // Клиенты.
    Route::prefix('customers')->controller(CustomerController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::put('{customer}', 'update');
    });

    // Заказы.
    Route::prefix('orders')->controller(OrderController::class)->group(function () {
        Route::get('', 'index');
        Route::get('/{order}', 'show');
        Route::post('', 'store');
        Route::put('/{order}', 'update');
        Route::post('/{order}/complete', 'complete');
        Route::post('/{order}/cancel', 'cancel');
        Route::post('/{order}/revive', 'revive');
    });

    // Поставки.
    Route::prefix('supplies')->controller(SupplyController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
    });

    // Перемещения между складами.
    Route::prefix('transfers')->controller(TransferController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::post('/{transfer}/complete', 'complete');
        Route::post('/{transfer}/cancel', 'cancel');
    });

    // История движения товаров.
    Route::get('/movements', [MovementController::class, 'index']);
});