<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchasePostRequest;
use App\Http\Resources\CheckResource;
use App\Jobs\CreditJob;
use App\Repositories\Contracts\CheckLogRepositoryInterface;
use App\Repositories\Contracts\CheckRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckController extends Controller
{
    private CheckRepositoryInterface $checkRepository;
    private CheckLogRepositoryInterface $checkLogRepository;


    public function __construct(
        CheckRepositoryInterface $checkRepository,
        CheckLogRepositoryInterface $checkLogRepository
    ) {
        $this->checkRepository = $checkRepository;
        $this->checkLogRepository = $checkLogRepository;
    }

    public function statusList(string $status): JsonResponse
    {
        if (!in_array($status, ['pending', 'rejected', 'approved'])) {
            return response()->json(['message' => 'Invalid payload!'], 403);
        }

        $checkCollection = $this->checkRepository->findByStatus($status);

        return response()->json(CheckResource::collection($checkCollection));
    }

    public function approve(Request $request, int $checkId): JsonResponse
    {
        $data = $request->all();

        $check = $this->checkRepository->find($checkId);

        $checkArray = $check->toArray();
        if ($checkArray['status'] !== 'pending') {
            return response()->json(['message' => 'Check not is pending!'], Response::HTTP_FORBIDDEN);
        }

        try {
            $data['check_id'] = $checkId;
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

        $check = $this->checkRepository->find($checkId);

        $checkData = $check->toArray();
        $checkData['status'] = 'rejected';
        $this->checkRepository->update($checkId, $checkData);

        $this->checkLogRepository->store(
            [
            'admin_id' => $data['admin_id'],
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
