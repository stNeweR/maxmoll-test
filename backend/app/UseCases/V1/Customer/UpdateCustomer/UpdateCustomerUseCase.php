<?php

namespace App\UseCases\V1\Customer\UpdateCustomer;

use App\Exceptions\BusinessException;
use App\Interfaces\CustomerRepositoryInterface;

/**
 * UseCase обновления клиента.
 */
final class UpdateCustomerUseCase
{
    public function __construct(private CustomerRepositoryInterface $customerRepository) {}

    public function execute(DataInput $input): DataOutput
    {
        $customer = $this->customerRepository->find($input->id);
        if ($customer === null) {
            throw new BusinessException('Клиент не найден.');
        }

        $customer = $this->customerRepository->update($customer, [
            'name' => $input->name,
            'phone' => $input->phone,
            'email' => $input->email,
        ]);

        return new DataOutput($customer);
    }
}
