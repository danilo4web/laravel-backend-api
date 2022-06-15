<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    private CustomerRepositoryInterface $customerRepository;
    private AccountRepositoryInterface $accountRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AccountRepositoryInterface $accountRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->accountRepository = $accountRepository;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $account = $this->createAnAccount($user->id, $data);
        $user['account_number'] = $account['number'];
        $user['account_id'] = $account['id'];

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
    }

    private function createAnAccount(int $userId, $data)
    {
        try {
            $customer = $this->customerRepository->store([
                'user_id' => $userId,
                'name' => $data['name'],
                'status' => 1,
                'address' => 'A'
            ]);

            return $this->accountRepository->store([
                'customer_id' => $customer->id,
                'number' => rand(10000000, 99999999),
                'status' => 1
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Something went wrong!', Response::HTTP_BAD_REQUEST]);
        }
    }
}
