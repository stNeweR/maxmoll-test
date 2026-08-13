<?php

namespace App\UseCases\V1\Customer\StoreCustomer;

use App\Interfaces\CustomerRepositoryInterface;

/**
 * UseCase создания клиента.
 */
final class StoreCustomerUseCase
{
    /**
     * @param CustomerRepositoryInterface $customerRepository репозиторий клиентов
     */
    public function __construct(private CustomerRepositoryInterface $customerRepository) {}

    /**
     * Создать клиента.
     *
     * @param DataInput $input входные данные клиента
     * @return DataOutput созданный клиент
     */
    public function execute(DataInput $input): DataOutput
    {
        $customer = $this->customerRepository->create([
            'name' => $input->name,
            'phone' => $input->phone,
            'email' => $input->email,
        ]);

        return new DataOutput($customer);
    }
}
