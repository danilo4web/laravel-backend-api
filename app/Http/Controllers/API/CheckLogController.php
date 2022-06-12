<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckLogResource;
use App\Repositories\Contracts\CheckLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckLogController extends Controller
{
    private CheckLogRepositoryInterface $checkLogRepository;

    public function __construct(CheckLogRepositoryInterface $checkLogRepository)
    {
        $this->checkLogRepository = $checkLogRepository;
    }

    public function index(): JsonResponse
    {
        $checkLog = $this->checkLogRepository->all();

        return response()->json(CheckLogResource::collection($checkLog), Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $checkLog = $this->checkLogRepository->store($data);

        return response()->json(new CheckLogResource($checkLog), Response::HTTP_CREATED);
    }

    public function show(int $checkLogId): JsonResponse
    {
        $checkLog = $this->checkLogRepository->find($checkLogId);

        return response()->json(new CheckLogResource($checkLog), Response::HTTP_OK);
    }

    public function update(Request $request, int $checkLogId): JsonResponse
    {
        $data = $request->all();

        $checkLog = $this->checkLogRepository->update($checkLogId, $data);

        return response()->json(new CheckLogResource($checkLog), Response::HTTP_OK);
    }

    public function delete(int $checkLogId): JsonResponse
    {
        $this->checkLogRepository->delete($checkLogId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
