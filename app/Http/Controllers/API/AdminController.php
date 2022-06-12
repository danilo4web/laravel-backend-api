<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index(): JsonResponse
    {
        $admin = $this->adminRepository->all();

        return response()->json(AdminResource::collection($admin), Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $admin = $this->adminRepository->store($data);

        return response()->json(new AdminResource($admin), Response::HTTP_CREATED);
    }

    public function show(int $adminId): JsonResponse
    {
        $admin = $this->adminRepository->find($adminId);

        return response()->json(new AdminResource($admin), Response::HTTP_OK);
    }

    public function update(Request $request, int $adminId): JsonResponse
    {
        $data = $request->all();

        $admin = $this->adminRepository->update($adminId, $data);

        return response()->json(new AdminResource($admin), Response::HTTP_OK);
    }

    public function delete(int $adminId): JsonResponse
    {
        $this->adminRepository->delete($adminId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
