<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\UserController;

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

// Grup untuk API versi 1
Route::prefix('v1')->group(function () {
    
    // --- Rute Publik (Tidak perlu login) ---
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

    // Rute untuk otentikasi Google
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

    // --- Rute Terproteksi (Wajib login/autentikasi) ---
    Route::middleware('auth:sanctum')->group(function () {
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // Mendapatkan data user yang sedang login
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Rute khusus untuk user
        Route::post('/user/increase-wallet-limit', [UserController::class, 'increaseWalletLimit']);
        
        // Rute resource untuk Wallets, Categories, Transactions, Budgets
        Route::apiResource('wallets', WalletController::class);
        Route::apiResource('categories', CategoryController::class)->except(['show']); // show tidak terlalu dibutuhkan
        Route::apiResource('transactions', TransactionController::class);
        Route::apiResource('budgets', BudgetController::class);

        // Rute untuk Laporan
        Route::get('/reports/monthly-summary', [ReportController::class, 'monthlySummary']);
    });
});

