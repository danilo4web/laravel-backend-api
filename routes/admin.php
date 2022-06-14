<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'adminLogin']);

Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('/register', [App\Http\Controllers\API\AdminController::class, 'register']);

    Route::get('/profile', function (Request $request) {
        return Auth::guard('admin')->user();
    });
    Route::put('/checks/{check}/approve', [App\Http\Controllers\API\CheckController::class, 'approve']);
    Route::put('/checks/{check}/reject', [App\Http\Controllers\API\CheckController::class, 'reject']);

    Route::get('checks/status/{status}', [App\Http\Controllers\API\CheckController::class, 'statusList']);
    Route::get('checks/{check}', [App\Http\Controllers\API\CheckController::class, 'show']);
    Route::get('checks', [App\Http\Controllers\API\CheckController::class, 'listChecks']);

    Route::get('accounts', [App\Http\Controllers\API\AccountController::class, 'index']);
});