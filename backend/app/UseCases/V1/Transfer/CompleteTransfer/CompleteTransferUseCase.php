<?php

namespace App\UseCases\V1\Transfer\CompleteTransfer;

use App\Exceptions\BusinessException;
use App\Interfaces\TransferRepositoryInterface;
use App\Services\StockService;

/**
 * UseCase операции «completeTransfer перемещения».
 */
final class CompleteTransferUseCase
{
    public function __construct(
        private TransferRepositoryInterface $transferRepository,
        private StockService $stockService,
    ) {}

    public function execute(int $transferId): DataOutput
    {
        $transfer = $this->transferRepository->find($transferId);
        if ($transfer === null) {
            throw new BusinessException('Перемещение не найдено.');
        }

        $this->stockService->completeTransfer($transfer);
        $transfer->load('fromWarehouse', 'toWarehouse', 'items.product');

        return new DataOutput($transfer);
    }
}
