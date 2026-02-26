<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function apply(Request $request)
    {
        $loan = $request->user()->loans()->create([
            'loan_type' => $request->loan_type,
            'monthly_income' => $request->monthly_income,
            'next_of_kin_name' => $request->next_of_kin_name,
            'next_of_kin_contact' => $request->next_of_kin_contact,
            'current_address' => $request->current_address,
        ]);

        return response()->json([
            'message' => 'Loan submitted',
            'loan' => $loan
        ]);
    }
}