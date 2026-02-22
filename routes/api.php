<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\LoanApplicationDetailController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loan-applications', [LoanApplicationController::class, 'store']);
Route::post('/loan-application-details', [LoanApplicationDetailController::class, 'store']);


Route::post('/forgot-password/send-code', [ForgotPasswordController::class, 'sendCode']);
Route::post('/forgot-password/verify-code', [ForgotPasswordController::class, 'verifyCode']);
Route::post('/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword']);