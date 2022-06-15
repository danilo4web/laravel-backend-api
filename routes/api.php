<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [App\Http\Controllers\API\UserController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get(
            '/profile', function (Request $request) {
                return Auth::user();
            }
        );
        Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    
        Route::post('accounts/resume/{month}', [App\Http\Controllers\API\AccountController::class, 'resume']);
        Route::get('accounts/{account}', [App\Http\Controllers\API\AccountController::class, 'show']);
        Route::post('accounts', [App\Http\Controllers\API\AccountController::class, 'store']);
        Route::put('accounts/{account}', [App\Http\Controllers\API\AccountController::class, 'update']);

        Route::post('checks', [App\Http\Controllers\API\CheckController::class, 'store']);
        Route::get('checks/status/{status}', [App\Http\Controllers\API\CheckController::class, 'statusList']);
        Route::get('checks/{check}', [App\Http\Controllers\API\CheckController::class, 'show']);
        Route::get('checks', [App\Http\Controllers\API\CheckController::class, 'listChecks']);

        Route::post('transactions/debits/{month}', [App\Http\Controllers\API\TransactionController::class, 'debitTransactionsPerMonth']);
        Route::post('transactions/credits/{month}', [App\Http\Controllers\API\TransactionController::class, 'creditTransactionsPerMonth']);
        Route::post('transactions/month/{month}', [App\Http\Controllers\API\TransactionController::class, 'transactionsPerMonth']);
        Route::get('transactions/{transaction}', [App\Http\Controllers\API\TransactionController::class, 'show']);
        Route::post('purchase', [App\Http\Controllers\API\TransactionController::class, 'addDebit']);
    }
);