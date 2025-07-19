<?php
namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;


interface TransactionRepositoryInterface
{
    public function transactionsHistory(int $userId) : Collection;
}