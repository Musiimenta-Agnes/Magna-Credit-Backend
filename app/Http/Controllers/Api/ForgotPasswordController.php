<?php

namespace App\Http\Controllers\Api; // adjust if different

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetCode(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    // Rate limiting (1 request per minute)
    $recent = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('created_at', '>', now()->subMinute())
        ->exists();

    if ($recent) {
        return response()->json([
            'message' => 'Please wait before requesting again.'
        ], 429);
    }

    // Generate 6-digit numeric code
    $code = mt_rand(100000, 999999);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        [
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
            'used' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    // Send email with code
    Mail::send('emails.forgot_password', [
        'token' => $code, // pass the 6-digit code
        'email' => $request->email
    ], function ($message) use ($request) {
        $message->to($request->email);
        $message->subject('Reset Your Password - Magna Credit');
    });

    return response()->json([
        'message' => 'Reset code sent successfully.'
    ]);
}
}