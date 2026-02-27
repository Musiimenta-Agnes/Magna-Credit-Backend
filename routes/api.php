 <?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\LoanApplicationDetailController;

use App\Http\Controllers\Api\PasswordResetController;


use App\Http\Controllers\Api\LoanController;

use App\Http\Controllers\Api\ProfileController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/loan-applications', [LoanApplicationController::class, 'store']);
Route::post('/loan-application-details', [LoanApplicationDetailController::class, 'store']);




Route::post('/forgot-password',    [PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-token', [PasswordResetController::class, 'verifyCode']);
Route::post('/reset-password',     [PasswordResetController::class, 'resetPassword']);



Route::middleware('auth:sanctum')->group(function () {
Route::post('/loan/apply', [LoanController::class, 'apply']);


Route::get('/profile',   [ProfileController::class, 'show']);
Route::patch('/profile', [ProfileController::class, 'update']);


}); 