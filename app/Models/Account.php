<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'status',
        'number',
        'customer_id'
    ];

    public function balance(Account $account)
    {
        return $account->balance;
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}
