<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RepaymentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/forgot-password',    [PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-token', [PasswordResetController::class, 'verifyCode']);
Route::post('/reset-password',     [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {

    // -- Profile --
    Route::get('/profile',   [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::post('/profile',  [ProfileController::class, 'update']);

    // -- Loan Applications --
    Route::post('/loan-applications',                [LoanApplicationController::class, 'store']);
    Route::get('/loan-applications',                 [LoanApplicationController::class, 'index']);
    Route::get('/loan-applications/{id}',            [LoanApplicationController::class, 'show']);
    Route::patch('/loan-applications/{id}',          [LoanApplicationController::class, 'update']);
    Route::post('/loan-applications/{id}/update',    [LoanApplicationController::class, 'update']);

    // -- Repayments --
    Route::get('/repayments',                [RepaymentController::class, 'index']);
    Route::post('/repayments',               [RepaymentController::class, 'store']);
    Route::get('/repayments/{loanId}',       [RepaymentController::class, 'byLoan']);

    // -- Dashboard --
    Route::get('/dashboard', [ProfileController::class, 'dashboard']);
});
