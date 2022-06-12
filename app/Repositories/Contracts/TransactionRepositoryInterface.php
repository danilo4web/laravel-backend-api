<?php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    public function all();

    public function find(int $id);

    public function store(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findByTypePerMonth(int $accountId, string $transactionType, string $month);

    public function getTotalTransactionsAmountPerMonth(int $accountId, string $month, string $transactionType);
}
