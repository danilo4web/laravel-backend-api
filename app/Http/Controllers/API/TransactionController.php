<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    private TransactionRepositoryInterface $transactionRepository;
    private AccountRepositoryInterface $accountRepository;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        AccountRepositoryInterface $accountRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->accountRepository = $accountRepository;
        $this->customerRepository = $customerRepository;
    }

    public function debitTransactionsPerMonth(Request $request, string $month): JsonResponse
    {
        $data = $request->all();

        $account = $this->getAccount();

        $transactions = $this->transactionRepository->findByTypePerMonth($account->id, 'debit', $month);

        return response()->json(
            TransactionResource::collection($transactions),
            Response::HTTP_OK
        );
    }

    public function creditTransactionsPerMonth(Request $request, string $month): JsonResponse
    {
        $data = $request->all();

        $account = $this->getAccount();

        $transactions = $this->transactionRepository->findByTypePerMonth($account->id, 'credit', $month);

        return response()->json(
            TransactionResource::collection($transactions),
            Response::HTTP_OK
        );
    }

    public function transactionsPerMonth(Request $request, string $month): JsonResponse
    {
        $data = $request->all();

        $account = $this->getAccount();

        $transactions = $this->transactionRepository->findPerMonth($account->id, $month);

        return response()->json(
            TransactionResource::collection($transactions),
            Response::HTTP_OK
        );
    }

    public function addDebit(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount' => 'required',
            'description' => 'required|string|max:255',
            'account_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $account = $this->getAccount();

        if ($account->balance < $data['amount']) {
            return response()->json(['message' => 'Account does not have enough money!'], 403);
        }

        $data['type'] = 'debit';
        $this->transactionRepository->store($data);

        $this->accountRepository->addDebit($account->id, $data['amount']);

        $balance = $this->accountRepository->getBalance($account->id);

        return response()->json(['message' => 'Done! New balance is: ' . number_format($balance, 2)], 201);
    }

    private function getAccount()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return null;
        }

        $customer = $this->customerRepository->findCustomerByUser($user->id);

        return $this->accountRepository->findAccountByCustomer($customer->id);
    }

    public function show(int $transactionId): JsonResponse
    {
        $transaction = $this->transactionRepository->find($transactionId);

        return response()->json(new TransactionResource($transaction), Response::HTTP_OK);
    }
}
