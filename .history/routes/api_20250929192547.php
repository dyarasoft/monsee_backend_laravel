<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // --- Public Routes ---
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // --- Protected Routes ---
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/getProfile', [AuthController::class, 'getProfile']);

        // User
        Route::post('/user/increase-wallet-limit', [UserController::class, 'increaseWalletLimit']);

        // Wallets
        Route::apiResource('wallets', WalletController::class);

        // Transactions
        Route::get('transactions', [TransactionController::class, 'index']); // URL updated
        Route::post('transactions', [TransactionController::class, 'store']);


        // Categories
        Route::apiResource('categories', CategoryController::class);

        // Budgets
        Route::apiResource('budgets', BudgetController::class);

        // Reports
        Route::get('reports/summary', [ReportController::class, 'summary']);
    });
});

