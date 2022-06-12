<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    public const ENABLE = 1;
    public const DISABLED = 0;

    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
