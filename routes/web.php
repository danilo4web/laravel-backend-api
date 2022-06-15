<?php

use App\Models\Account;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/', function () {
        return 'Welcome to the BNB Bank API. It work\'s!';
    }
);

Route::get(
    'cache-clear',
    function () {
        Artisan::call('config:cache');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
    }
);