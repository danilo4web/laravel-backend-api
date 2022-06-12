<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    private TransactionRepositoryInterface $transactionRepository;
    private AccountRepositoryInterface $accountRepository;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function index(): JsonResponse
    {
        $account = $this->accountRepository->all();

        return response()->json(AccountResource::collection($account), Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(
            [
            'number' => 'required',
            'customer_id' => 'required|numeric'
            ]
        );

        $account = $this->accountRepository->store($data);

        return response()->json(new AccountResource($account), Response::HTTP_CREATED);
    }

    public function show(int $accountId): JsonResponse
    {
        $account = $this->accountRepository->find($accountId);

        return response()->json(new AccountResource($account), Response::HTTP_OK);
    }

    public function update(Request $request, int $accountId): JsonResponse
    {
        $data = $request->all();

        $account = $this->accountRepository->update($accountId, $data);

        return response()->json(new AccountResource($account), Response::HTTP_OK);
    }

    public function delete(int $accountId): JsonResponse
    {
        $this->accountRepository->delete($accountId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function resume(Request $request, string $month)
    {
        $data = $request->all();

        $balance = $this->accountRepository->getBalance($data['account_id']);

        $expenseAmount = $this->transactionRepository->getTotalTransactionsAmountPerMonth(
            $data['account_id'],
            $month,
            'debit'
        );

        $incomesAmount = $this->transactionRepository->getTotalTransactionsAmountPerMonth(
            $data['account_id'],
            $month,
            'credit'
        );

        return response()->json([
            'balance' => $balance,
            'expenseAmount' => $expenseAmount,
            'incomesAmount' => $incomesAmount
        ], Response::HTTP_OK);
    }
}
