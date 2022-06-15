<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    private TransactionRepositoryInterface $transactionRepository;
    private AccountRepositoryInterface $accountRepository;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->customerRepository = $customerRepository;
        
    }

    public function show(int $accountId): JsonResponse
    {
        $account = $this->accountRepository->find($accountId);

        return response()->json(new AccountResource($account), Response::HTTP_OK);
    }

    public function resume(Request $request, string $month)
    {
        $data = $request->all();

        $accountId = $this->getAccountId();

        $balance = $this->accountRepository->getBalance($accountId);

        $expenseAmount = $this->transactionRepository->getTotalTransactionsAmountPerMonth(
            $accountId,
            $month,
            'debit'
        );

        $incomesAmount = $this->transactionRepository->getTotalTransactionsAmountPerMonth(
            $accountId,
            $month,
            'credit'
        );

        return response()->json([
            'balance' => $balance,
            'expenseAmount' => $expenseAmount,
            'incomesAmount' => $incomesAmount
        ], Response::HTTP_OK);
    }

    private function getAccountId()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return null;
        }

        $customer = $this->customerRepository->findCustomerByUser($user->id);

        $account = $this->accountRepository->findAccountByCustomer($customer->id);

        return $account->id ?? null;
    }
}
