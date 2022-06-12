<?php

namespace App\Repositories\Eloquent;

use App\Models\CheckLog;
use App\Repositories\Contracts\CheckLogRepositoryInterface;

class CheckLogRepository extends AbstractRepository implements CheckLogRepositoryInterface
{
    protected $model = CheckLog::class;
}
