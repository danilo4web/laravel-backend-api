<?php

namespace App\Repositories\Eloquent;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;

class AdminRepository extends AbstractRepository implements AdminRepositoryInterface
{
    protected $model = Admin::class;
}
