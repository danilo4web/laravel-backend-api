<?php

namespace App\Repositories\Eloquent;

use App\Models\Check;
use App\Repositories\Contracts\CheckRepositoryInterface;

class CheckRepository extends AbstractRepository implements CheckRepositoryInterface
{
    protected $model = Check::class;

    public function findByStatus(string $status)
    {
        return Check::where('status', $status)->get();
    }
}
