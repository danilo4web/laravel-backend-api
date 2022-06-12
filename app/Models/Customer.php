<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'user_id',
        'address'
    ];

    public function user()
    {
        return $this
            ->belongsTo(User::class, 'users')
            ->withPivot(
                'id',
                'name',
                'email'
            );
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
