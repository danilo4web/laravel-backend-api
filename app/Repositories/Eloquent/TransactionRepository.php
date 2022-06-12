<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
    protected $model = Transaction::class;

    public function findByTypePerMonth(int $accountId, string $transactionType, string $month)
    {
        list($year, $month) = explode("-", $month);

        return Transaction::select('description', 'created_at as date', 'amount')
            ->where('type', $transactionType)
            ->where('account_id', $accountId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function getTotalTransactionsAmountPerMonth(int $accountId, string $month, string $transactionType)
    {
        list($year, $month) = explode("-", $month);

        return Transaction::where('type', $transactionType)
            ->where('account_id', $accountId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');
    }
}
