 <?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\LoanApplicationDetailController;


use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PasswordResetController;


use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LoanController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loan-applications', [LoanApplicationController::class, 'store']);
Route::post('/loan-application-details', [LoanApplicationDetailController::class, 'store']);




// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
// Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
// Route::post('/forgot-password', [PasswordResetController::class, 'sendResetCode']);


Route::post('/password/send-code', [PasswordResetController::class, 'sendResetCode']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);


// Route::post('password/send-code', [PasswordResetController::class, 'sendResetCode']);
// Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);




Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/loan/apply', [LoanController::class, 'apply']);

}); 