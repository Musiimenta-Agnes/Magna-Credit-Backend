 <?php
 
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\LoanApplicationController;

use App\Http\Controllers\Api\PasswordResetController;

use App\Http\Controllers\Api\ProfileController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);





Route::post('/forgot-password',    [PasswordResetController::class, 'sendCode']);
Route::post('/verify-reset-token', [PasswordResetController::class, 'verifyCode']);
Route::post('/reset-password',     [PasswordResetController::class, 'resetPassword']);


Route::get('/profile',   [ProfileController::class, 'show']);
Route::patch('/profile', [ProfileController::class, 'update']);




Route::middleware('auth:sanctum')->group(function () {

    // ── Single endpoint for the full loan application ──
    Route::post('/loan-applications', [LoanApplicationController::class, 'store']);
    Route::get('/loan-applications', [LoanApplicationController::class, 'index']);
    Route::get('/loan-applications/{id}', [LoanApplicationController::class, 'show']);

 

});