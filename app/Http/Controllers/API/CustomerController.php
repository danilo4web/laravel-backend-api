<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(): JsonResponse
    {
        $customer = $this->customerRepository->all();

        return response()->json(CustomerResource::collection($customer), Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }


        $customer = $this->customerRepository->store($data);

        return response()->json(new CustomerResource($customer), Response::HTTP_CREATED);
    }

    public function show(int $customerId): JsonResponse
    {
        $customer = $this->customerRepository->find($customerId);

        return response()->json(new CustomerResource($customer), Response::HTTP_OK);
    }

    public function update(Request $request, int $customerId): JsonResponse
    {
        $data = $request->all();

        $customer = $this->customerRepository->update($customerId, $data);

        return response()->json(new CustomerResource($customer), Response::HTTP_OK);
    }

    public function delete(int $customerId): JsonResponse
    {
        $this->customerRepository->delete($customerId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
