<?php

namespace App\UseCases\V1\Customer\ListCustomers;

use App\Interfaces\CustomerRepositoryInterface;

/**
 * UseCase получения списка клиентов с фильтрами и пагинацией.
 */
final class ListCustomersUseCase
{
    /**
     * @param CustomerRepositoryInterface $customerRepository репозиторий клиентов
     */
    public function __construct(private CustomerRepositoryInterface $customerRepository) {}

    /**
     * Получить список клиентов.
     *
     * @param DataInput $input входные данные (фильтры и пагинация)
     * @return DataOutput список клиентов и метаданные пагинации
     */
    public function execute(DataInput $input): DataOutput
    {
        $paginated = $this->customerRepository->paginate($input->search, $input->email, $input->phone, $input->perPage);
        $rows = collect($paginated->items())->map->toArray()->all();

        return new DataOutput(
            $rows,
            [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        );
    }
}
