<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'code' => 'required|digits:6',
        'password' => 'required|min:6|confirmed'
    ]);

    $reset = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('used', false)
        ->latest()
        ->first();

    if (!$reset) {
        return response()->json(['message' => 'Invalid request.'], 400);
    }

    if (now()->greaterThan($reset->expires_at)) {
        return response()->json(['message' => 'Code expired.'], 400);
    }

    if ($reset->code != $request->code) {
        return response()->json(['message' => 'Invalid code.'], 400);
    }

    // Update password
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    // Mark code as used
    DB::table('password_resets')
        ->where('id', $reset->id)
        ->update(['used' => true]);

    return response()->json([
        'message' => 'Password reset successful.'
    ]);
}
}