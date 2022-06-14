<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository extends AbstractRepository implements CustomerRepositoryInterface
{
    protected $model = Customer::class;

    public function findCustomerByUser(int $userId)
    {
        return Customer::where('user_id', $userId)->first();
    }

    public function account(int $customerId)
    {
        return Account::where('customer_id', $customerId)->first();
    }
}
