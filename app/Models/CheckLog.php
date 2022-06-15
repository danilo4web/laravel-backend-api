<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CheckLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'check_id',
        'status'
    ];

    public function rules()
    {
        return [
            'admin_id' => 'required|numeric',
            'check_id' => 'required|numeric',
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])]
        ];
    }

    public function check()
    {
        return $this->hasOne(Check::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }
}
