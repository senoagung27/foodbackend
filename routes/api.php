<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\FoodController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\TransactionController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('register', [RegisterController::class, 'register']);
// Route::post('login', [RegisterController::class, 'login']);
//API route for register new user



Route::prefix('/v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    //API route for login user
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('food', [FoodController::class, 'all']);

    Route::post('midtrans/callback', [MidtransController::class, 'callback']);

    Route::middleware('auth:sanctum')->group( function () {
        Route::resource('products', ProductController::class);
        Route::get('transaction', [TransactionController::class, 'all']);
        Route::post('transaction/{id}', [TransactionController::class, 'update']);
        Route::post('checkout', [TransactionController::class, 'checkout']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::get('/profile', function(Request $request) {
            return auth()->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

