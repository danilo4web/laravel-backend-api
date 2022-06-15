<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private AdminRepositoryInterface $adminRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(AdminRepositoryInterface $adminRepository, UserRepositoryInterface $userRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        if (!Auth::attempt(['email' => $request->email,'password' => $request->password,'is_active' => 1])) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        config(['sanctum.guard' => 'user']);

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

    public function adminLogin(Request $request)
    {

        $admin = $this->adminRepository->findByEmail($request->email);

        if (!$admin || !$admin->is_active || !Hash::check($request->password, $admin->password)) {
            return response()
               ->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        config(['sanctum.guard' => 'admin']);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()
            ->json([
                'message' => $admin->name . ' you are logged.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'id' => $admin->id
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
