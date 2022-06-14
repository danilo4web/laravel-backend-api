<?php

namespace App\Repositories\Eloquent;

use App\Models\Check;
use App\Repositories\Contracts\CheckRepositoryInterface;

class CheckRepository extends AbstractRepository implements CheckRepositoryInterface
{
    protected $model = Check::class;

    public function findByStatus(string $status)
    {
        return Check::where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function findByAccount(int $accountId)
    {
        return Check::where('account_id', $accountId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function findByAccountAndStatus(int $accountId, string $status)
    {
        return Check::where('account_id', $accountId)
            ->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}
