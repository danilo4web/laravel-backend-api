<?php

namespace App\Repositories\Contracts;

interface AccountRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function store(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function addCredit(int $accountId, float $amount);

    public function addDebit(int $accountId, float $amount);

    public function getBalance(int $accountId);
}
