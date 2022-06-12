<?php

use Illuminate\Http\Request;
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

Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

Route::group(
    ['middleware' => ['auth:sanctum']], function () {
        Route::get(
            '/profile', function (Request $request) {
                return auth()->user();
            }
        );
        Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    
        Route::get('accounts', [App\Http\Controllers\API\AccountController::class, 'index']);
        Route::post('accounts/resume/{month}', [App\Http\Controllers\API\AccountController::class, 'resume']);
        Route::get('accounts/{account}', [App\Http\Controllers\API\AccountController::class, 'show']);
        Route::post('accounts', [App\Http\Controllers\API\AccountController::class, 'store']);
        Route::put('accounts/{account}', [App\Http\Controllers\API\AccountController::class, 'update']);
        Route::delete('accounts/{account}', [App\Http\Controllers\API\AccountController::class, 'delete']);

        Route::get('customers', [App\Http\Controllers\API\CustomerController::class, 'index']);
        Route::get('customers/{customer}', [App\Http\Controllers\API\CustomerController::class, 'show']);
        Route::post('customers', [App\Http\Controllers\API\CustomerController::class, 'store']);
        Route::put('customers/{customer}', [App\Http\Controllers\API\CustomerController::class, 'update']);
        Route::delete('customers/{customer}', [App\Http\Controllers\API\CustomerController::class, 'delete']);

        Route::get('checks/status/{status}', [App\Http\Controllers\API\CheckController::class, 'statusList']);
        Route::get('checks/{check}', [App\Http\Controllers\API\CheckController::class, 'show']);
        Route::post('checks', [App\Http\Controllers\API\CheckController::class, 'store']);
        Route::put('checks/{check}/approve', [App\Http\Controllers\API\CheckController::class, 'approve']);
        Route::put('checks/{check}/reject', [App\Http\Controllers\API\CheckController::class, 'reject']);

        Route::post('transactions/debits/{month}', [App\Http\Controllers\API\TransactionController::class, 'debitTransactionsPerMonth']);
        Route::post('transactions/credits/{month}', [App\Http\Controllers\API\TransactionController::class, 'creditTransactionsPerMonth']);
        Route::post('transactions/month/{month}', [App\Http\Controllers\API\TransactionController::class, 'transactionsPerMonth']);
        Route::get('transactions/{transaction}', [App\Http\Controllers\API\TransactionController::class, 'show']);
        Route::post('purchase', [App\Http\Controllers\API\TransactionController::class, 'addDebit']);
    }
);