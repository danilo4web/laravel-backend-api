<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository extends AbstractRepository implements CustomerRepositoryInterface
{
    protected $model = Customer::class;

    public function findCustomerByUser(int $userId)
    {
        return Customer::where('user_id', $userId)->first();
    }
}
