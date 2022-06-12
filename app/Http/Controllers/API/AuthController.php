<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
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

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;
        $customer = Customer::where('user_id', $user->id)->first();

        $account = Account::where('customer_id', $customer->id)->first();

        return response()
            ->json([
                'message' => 'Hi ' . $user->name . ', welcome to BNB Back',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'id' => $user->id,
                'account_number' => $account->number,
                'account_id' => $account->id
            ]);
    }

    public function logout()
    {
        Auth::user()->tokens->each(
            function ($token, $key) {
                $token->delete();
            }
        );

        return ['message' => 'You have successfully logged out and the token was successfully deleted'];
    }
}
