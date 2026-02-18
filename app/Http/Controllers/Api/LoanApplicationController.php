<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    /**
     * Store a new loan application
     */
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'bio_info' => 'nullable|string',
            'location' => 'required|string|max:255',
            'other_contact' => 'nullable|string|max:50',
            'gender' => 'required|string|in:Male,Female,Other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Save loan application
        $loanApplication = LoanApplication::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'contact' => $request->contact,
            'email' => $request->email,
            'bio_info' => $request->bio_info,
            'location' => $request->location,
            'other_contact' => $request->other_contact,
            'gender' => $request->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Loan application submitted successfully',
            'data' => $loanApplication
        ], 201);
    }
}
