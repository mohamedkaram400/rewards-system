<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use Exception;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    /**
     * RedemptionController constructor.
     *
     * @param TransactionService $transactionService
     */
    public function __construct(
        private TransactionService $transactionService
    ) {}
    
    public function transactionsHistory(int $userId): JsonResponse
    {
        try {
            $transactions = $this->transactionService->transactionsHistory($userId);

            if ($transactions->isEmpty()) {
                throw new NotFoundException('No transactions found');
            }

            return $this->apiResponse('Transactions returned successfull', 200, $transactions);

        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
}
