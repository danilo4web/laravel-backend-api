<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Repositories\Contracts\AccountRepositoryInterface;

class AccountRepository extends AbstractRepository implements AccountRepositoryInterface
{
    protected $model = Account::class;

    public function addCredit(int $accountId, float $amount)
    {
        $account = Account::find($accountId);
        $account->balance += $amount;
        return $account->update($account->toArray());
    }

    public function addDebit(int $accountId, float $amount)
    {
        $account = Account::find($accountId);
        $account->balance -= $amount;
        return $account->update($account->toArray());
    }

    public function getBalance(int $accountId)
    {
        return Account::find($accountId)->value('balance');
    }
}
