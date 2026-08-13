<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Кастомный Eloquent-построитель запросов для модель Customer.
 *
 * Содержит переиспользуемые методы фильтрации списка клиентов.
 */
class CustomerQueryBuilder extends Builder
{
    /**
     * Фильтр по имени клиента (частичное совпадение).
     */
    public function search(?string $term): self
    {
        if ($term !== null && $term !== '') {
            $this->where('name', 'like', "%{$term}%");
        }
        return $this;
    }

    /**
     * Фильтр по точному e-mail.
     */
    public function byEmail(?string $email): self
    {
        if ($email !== null && $email !== '') {
            $this->where('email', $email);
        }
        return $this;
    }

    /**
     * Фильтр по точному телефону.
     */
    public function byPhone(?string $phone): self
    {
        if ($phone !== null && $phone !== '') {
            $this->where('phone', $phone);
        }
        return $this;
    }
}