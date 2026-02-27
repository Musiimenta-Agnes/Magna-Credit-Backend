<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * POST /api/forgot-password
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,  // ← ADDED so Flutter catches this as an error
                'message' => 'No account found with that email address.',
            ], 404);
        }

        // Delete any existing codes for this email (allow resend)
        PasswordResetCode::where('email', $request->email)->delete();

        // Generate a random 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save to DB with 10-minute expiry
        PasswordResetCode::create([
            'email'      => $request->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send the email
        Mail::to($user->email)->send(new ForgotPasswordMail($user, $code));

        return response()->json([
            'message' => 'A 6-digit verification code has been sent to your email.',
        ]);
    }

    /**
     * POST /api/verify-reset-token
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        $record = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->token)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid verification code. Please try again.',
            ], 422);
        }

        if ($record->isExpired()) {
            $record->delete();
            return response()->json([
                'message' => 'This code has expired. Please request a new one.',
            ], 422);
        }

        return response()->json([
            'message' => 'Code verified successfully.',
            'token'   => $request->token,
        ]);
    }

    /**
     * POST /api/reset-password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $record = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->token)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired code. Please start over.',
            ], 422);
        }

        if ($record->isExpired()) {
            $record->delete();
            return response()->json([
                'message' => 'This code has expired. Please request a new one.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the used code so it can't be reused
        $record->delete();

        return response()->json([
            'message' => 'Password reset successfully. You can now log in.',
        ]);
    }
}











// use App\Http\Controllers\Controller;
// use App\Mail\ForgotPasswordMail;
// use App\Models\PasswordResetCode;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Mail;

// class PasswordResetController extends Controller
// {
//     /**
//      * POST /api/forgot-password
//      *
//      * Generates a 6-digit code, saves it to DB,
//      * and emails it to the user.
//      * Called by Flutter ForgotPasswordPage.
//      */
//     public function sendCode(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//         ]);

//         // Check if email exists in users table
//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json([
//                 'message' => 'No account found with that email address.',
//             ], 404);
//         }

//         // Delete any existing codes for this email (allow resend)
//         PasswordResetCode::where('email', $request->email)->delete();

//         // Generate a random 6-digit code
//         $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

//         // Save to DB with 10-minute expiry
//         PasswordResetCode::create([
//             'email'      => $request->email,
//             'code'       => $code,
//             'expires_at' => now()->addMinutes(10),
//         ]);

//         // Send the email
//         Mail::to($user->email)->send(new ForgotPasswordMail($user, $code));

//         return response()->json([
//             'message' => 'A 6-digit verification code has been sent to your email.',
//         ]);
//     }

//     /**
//      * POST /api/verify-reset-token
//      *
//      * Validates the 6-digit code entered by the user.
//      * Called by Flutter VerifyCodePage.
//      */
//     public function verifyCode(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'token' => 'required|string', // Flutter sends the code as 'token'
//         ]);

//         $record = PasswordResetCode::where('email', $request->email)
//             ->where('code', $request->token)
//             ->first();

//         // Code not found
//         if (!$record) {
//             return response()->json([
//                 'message' => 'Invalid verification code. Please try again.',
//             ], 422);
//         }

//         // Code has expired
//         if ($record->isExpired()) {
//             $record->delete();
//             return response()->json([
//                 'message' => 'This code has expired. Please request a new one.',
//             ], 422);
//         }

//         return response()->json([
//             'message' => 'Code verified successfully.',
//             'token'   => $request->token, // pass back so Flutter can use it in next step
//         ]);
//     }

//     /**
//      * POST /api/reset-password
//      *
//      * Resets the user's password after code is verified.
//      * Called by Flutter ResetPasswordPage.
//      */
//     public function resetPassword(Request $request)
//     {
//         $request->validate([
//             'email'                 => 'required|email',
//             'token'                 => 'required|string',
//             'password'              => 'required|string|min:8',
//             'password_confirmation' => 'required|same:password',
//         ]);

//         // Re-verify the code one last time for security
//         $record = PasswordResetCode::where('email', $request->email)
//             ->where('code', $request->token)
//             ->first();

//         if (!$record) {
//             return response()->json([
//                 'message' => 'Invalid or expired code. Please start over.',
//             ], 422);
//         }

//         if ($record->isExpired()) {
//             $record->delete();
//             return response()->json([
//                 'message' => 'This code has expired. Please request a new one.',
//             ], 422);
//         }

//         // Find the user and update their password
//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json([
//                 'message' => 'User not found.',
//             ], 404);
//         }

//         $user->update([
//             'password' => Hash::make($request->password),
//         ]);

//         // Delete the used code so it can't be reused
//         $record->delete();

//         return response()->json([
//             'message' => 'Password reset successfully. You can now log in.',
//         ]);
//     }
// }


