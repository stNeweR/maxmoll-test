<?php

namespace App\Interfaces;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Интерфейс репозитория клиентов.
 *
 * Описывает операции доступа к данным таблицы customers.
 */
interface CustomerRepositoryInterface
{
    /**
     * Находит клиента по идентификатору.
     */
    public function find(int $id): ?Customer;

    /**
     * Создаёт клиента.
     */
    public function create(array $attributes): Customer;

    /**
     * Обновляет клиента и возвращает его актуальное состояние.
     */
    public function update(Customer $customer, array $attributes): Customer;

    /**
     * Возвращает список клиентов с фильтрами и пагинацией.
     *
     * @param  string|null  $search  частичное совпадение по имени
     * @param  string|null  $email  точное совпадение по e-mail
     * @param  string|null  $phone  точное совпадение по телефону
     */
    public function paginate(?string $search, ?string $email, ?string $phone, int $perPage): LengthAwarePaginator;
}
