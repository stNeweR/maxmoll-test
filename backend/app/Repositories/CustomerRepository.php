<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Репозиторий клиентов.
 *
 * Реализует доступ к данным таблицы customers.
 */
class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function find(int $id): ?Customer
    {
        return Customer::find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes): Customer
    {
        return Customer::create($attributes);
    }

    /**
     * {@inheritDoc}
     */
    public function update(Customer $customer, array $attributes): Customer
    {
        $customer->update($attributes);

        return $customer->refresh();
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?string $search, ?string $email, ?string $phone, int $perPage): LengthAwarePaginator
    {
        $query = Customer::query()->orderBy('id');

        // Фильтрация по частичному совпадению имени.
        if ($search !== null && $search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }
        // Фильтрация по точному e-mail.
        if ($email !== null && $email !== '') {
            $query->where('email', $email);
        }
        // Фильтрация по точному телефону.
        if ($phone !== null && $phone !== '') {
            $query->where('phone', $phone);
        }

        return $query->paginate($perPage);
    }
}
