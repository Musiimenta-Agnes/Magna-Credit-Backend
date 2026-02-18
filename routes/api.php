<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\LoanApplicationDetailController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loan-applications', [LoanApplicationController::class, 'store']);
Route::post('/loan-application-details', [LoanApplicationDetailController::class, 'store']);
