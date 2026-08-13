<?php

namespace App\UseCases\V1\Customer\StoreCustomer;

use App\Interfaces\CustomerRepositoryInterface;

/**
 * UseCase создания клиента.
 */
final class StoreCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $customerRepository) {}

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
