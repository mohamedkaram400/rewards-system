<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionService
{
    /**
     * RedemptionController constructor.
     *
     * @param TransactionService $transactionService
     */
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}
    
    
    /**
     * Redeem a product using user's points.
     *
     * @param int $userId
     * @return \App\Models\TransactionHistory $collection
     *
     */
    public function transactionsHistory($userId): Collection
    {
        return $this->transactionRepository->transactionsHistory($userId);
    }
}