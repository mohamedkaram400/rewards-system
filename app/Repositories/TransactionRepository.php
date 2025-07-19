<?php
namespace App\Repositories;

use App\Models\TransactionHistory;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function transactionsHistory($userId): Collection
    {
        return TransactionHistory::where('user_id', $userId)->get();
    }
}