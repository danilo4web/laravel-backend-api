<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const DEBIT = 'debit';
    public const CREDIT = 'credit';

    protected $fillable = [
        'amount',
        'type',
        'account_id',
        'check_id',
        'description'
    ];

    public function check()
    {
        return $this
            ->belongsTo(Check::class, 'checks')
            ->withPivot(
                'id',
                'file',
                'description',
                'amount',
                'status',
                'account_id'
            );
    }

    public function account()
    {
        return $this
            ->belongsTo(Account::class, 'checks')
            ->withPivot(
                'id',
                'file',
                'description',
                'amount',
                'status',
                'account_id'
            );
    }
}
