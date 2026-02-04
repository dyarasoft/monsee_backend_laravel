<?php

use App\Http\Controllers\Api\V1\App\AuthController;
use App\Http\Controllers\Api\V1\App\BudgetController;
use App\Http\Controllers\Api\V1\App\CategoryController;
use App\Http\Controllers\Api\V1\App\ReportController;
use App\Http\Controllers\Api\V1\App\TransactionController;
use App\Http\Controllers\Api\V1\App\UserController;
use App\Http\Controllers\Api\V1\App\WalletController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // === ROUTE For Admin === //
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

    // === EMAIL VERIFICATION ===
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');

    // === ROUTE For App === //
    
    Route::post('/app/auth/google', [AuthController::class, 'loginWithGoogle'])->middleware('throttle:6,1');

    // Guest Features
    Route::get('/app/categories/public', [CategoryController::class, 'publicIndex']);

    
    // === PROTECTED ROUTES (Requires Authentication) ===
    Route::middleware('auth:sanctum')->prefix('app')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('user/profile', [AuthController::class, 'profile']);
        Route::post('/user/deactivate', [AuthController::class, 'deactivateAccount']);

     

        Route::post('/user/increase-wallet-limit', [UserController::class, 'increaseWalletLimit']);

        Route::apiResource('wallets', WalletController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::get('/transactions/recent', [TransactionController::class, 'recent']);
        Route::apiResource('transactions', TransactionController::class)->except(['index']);
        Route::get('transactions', [TransactionController::class, 'index']); 
        Route::apiResource('budgets', BudgetController::class);

        // Reports
        Route::get('/reports/summary', [ReportController::class, 'summary']);
    });

    
});

