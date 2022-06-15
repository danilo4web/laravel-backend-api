<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckResource;
use App\Jobs\CreditJob;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CheckLogRepositoryInterface;
use App\Repositories\Contracts\CheckRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckController extends Controller
{
    private CheckRepositoryInterface $checkRepository;
    private CheckLogRepositoryInterface $checkLogRepository;
    private CustomerRepositoryInterface $customerRepository;
    private AccountRepositoryInterface $accountRepository;

    public function __construct(
        CheckRepositoryInterface $checkRepository,
        CheckLogRepositoryInterface $checkLogRepository,
        CustomerRepositoryInterface $customerRepository,
        AccountRepositoryInterface $accountRepository
    ) {
        $this->checkRepository = $checkRepository;
        $this->checkLogRepository = $checkLogRepository;
        $this->customerRepository = $customerRepository;
        $this->accountRepository = $accountRepository;
    }

    public function statusList(string $status): JsonResponse
    {
        if (!in_array($status, ['pending', 'rejected', 'approved'])) {
            return response()->json(['message' => 'Invalid payload!'], 403);
        }

        $accountId = $this->getAccountId();
        if (!$accountId) {
            $checkCollection = $this->checkRepository->findByStatus($status);

            return response()->json(CheckResource::collection($checkCollection));
        }

        $checkCollection = $this->checkRepository->findByAccountAndStatus($accountId, $status);

        return response()->json(CheckResource::collection($checkCollection));
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


    public function listChecks()
    {
        $accountId = $this->getAccountId();

        if (!$accountId) {
            $checks = $this->checkRepository->all();

            return response()->json(CheckResource::collection($checks));
        }

        $checks = $this->checkRepository->findByAccount($accountId);

        return response()->json(CheckResource::collection($checks));
    }

    public function listPendingChecks()
    {
        $checks = $this->checkRepository->findByStatus('pending');

        return response()->json(CheckResource::collection($checks));
    }

    public function approve(Request $request, int $checkId): JsonResponse
    {
        $data = $request->all();

        $admin = Auth::guard('admin')->user();

        $check = $this->checkRepository->find($checkId);

        if (!$check) {
            return response()->json(['message' => 'Check not found!'], Response::HTTP_NOT_FOUND);
        }

        $checkArray = $check->toArray();
        if ($checkArray['status'] !== 'pending') {
            return response()->json(['message' => 'Check not is pending!'], Response::HTTP_FORBIDDEN);
        }

        try {
            $data['check_id'] = $checkId;
            $data['admin_id'] = $admin->id;
            CreditJob::dispatch($data);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!'], 500);
        }

        return response()->json(['message' => 'Check approved: ' . $checkArray['id']], 200);
    }

    public function reject(Request $request, int $checkId)
    {
        $data = $request->all();

        $admin = Auth::guard('admin')->user();

        $check = $this->checkRepository->find($checkId);

        if (!$check) {
            return response()->json(['message' => 'Check not found!'], Response::HTTP_NOT_FOUND);
        }

        if ($check['status'] !== 'pending') {
            return response()->json(['message' => 'Check is not pending!'], Response::HTTP_FORBIDDEN);
        }

        $checkData = $check->toArray();
        $checkData['status'] = 'rejected';
        $this->checkRepository->update($checkId, $checkData);

        $this->checkLogRepository->store(
            [
            'admin_id' => $admin->id,
            'check_id' => $checkId,
            'status' => 'rejected'
            ]
        );

        return response()->json($checkData, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['status'] = 'pending';
        $data['account_id'] = $this->getAccountId();

        try {
            $check = $this->checkRepository->store($data);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return response()->json(new CheckResource($check), Response::HTTP_CREATED);
    }

    public function show(int $checkId): JsonResponse
    {
        $check = $this->checkRepository->find($checkId);

        return response()->json(new CheckResource($check), Response::HTTP_OK);
    }
}
