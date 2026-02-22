<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1️⃣ SEND CODE
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $code = rand(100000, 999999);

        $user->reset_code = $code;
        $user->reset_code_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        Mail::raw("Your verification code is: $code", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset Code');
        });

        return response()->json([
            'message' => 'Verification code sent'
        ]);
    }

    // 2️⃣ VERIFY CODE
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_code', $request->code)
            ->where('reset_code_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired code'
            ], 400);
        }

        return response()->json([
            'message' => 'Code verified'
        ]);
    }

    // 3️⃣ RESET PASSWORD
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'password' => 'required|min:6'
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_code', $request->code)
            ->where('reset_code_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired code'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->reset_code = null;
        $user->reset_code_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
}