<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'profile' => $user->profile,
            'loan' => $user->loans()->latest()->first()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $loan = $user->loans()->latest()->first();

        if ($loan && $loan->status === 'approved') {
            return response()->json(['message' => 'Profile locked'], 403);
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'other_contact',
                'address',
                'bio',
                'gender',
                'occupation',
                'education'
            ])
        );

        return response()->json(['message' => 'Updated']);
    }
}