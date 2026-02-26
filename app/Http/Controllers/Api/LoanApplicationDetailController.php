<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplicationDetail;
use Illuminate\Support\Facades\Validator;

class LoanApplicationDetailController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_application_id' => 'required|exists:loan_applications,id',
            'kin_name'            => 'required|string',
            'kin_contact'         => 'required|string',
            'occupation'          => 'required|string',
            'monthly_income'      => 'required|numeric',
            'loan_amount'         => 'required|numeric',   // ← NEW
            'loan_type'           => 'required|string',
            'education'           => 'required|string',
            'address'             => 'required|string',
            'national_id_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'collateral_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Upload National ID
        $nationalIdPath = $request->file('national_id_image')
                                  ->store('national_ids', 'public');

        // Upload Collateral Images
        $collateralPaths = [];
        if ($request->hasFile('collateral_images')) {
            foreach ($request->file('collateral_images') as $image) {
                $collateralPaths[] = $image->store('collaterals', 'public');
            }
        }

        $detail = LoanApplicationDetail::create([
            'loan_application_id' => $request->loan_application_id,
            'kin_name'            => $request->kin_name,
            'kin_contact'         => $request->kin_contact,
            'occupation'          => $request->occupation,
            'monthly_income'      => $request->monthly_income,
            'loan_amount'         => $request->loan_amount,   // ← NEW
            'loan_type'           => $request->loan_type,
            'education'           => $request->education,
            'address'             => $request->address,
            'national_id_image'   => $nationalIdPath,
            'collateral_images'   => $collateralPaths,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Loan application details submitted successfully',
            'data'    => $detail
        ], 201);
    }
}










// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\LoanApplicationDetail;
// use Illuminate\Support\Facades\Validator;

// class LoanApplicationDetailController extends Controller
// {
//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'loan_application_id' => 'required|exists:loan_applications,id',
//             'kin_name' => 'required|string',
//             'kin_contact' => 'required|string',
//             'occupation' => 'required|string',
//             'monthly_income' => 'required|numeric',
//             'loan_type' => 'required|string',
//             'education' => 'required|string',
//             'address' => 'required|string',
//             'national_id_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
//             'collateral_images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
//         ]);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'errors' => $validator->errors()
//             ], 422);
//         }

//         // Upload National ID
//         $nationalIdPath = $request->file('national_id_image')
//                                   ->store('national_ids', 'public');

//         // Upload Collateral Images
//         $collateralPaths = [];

//         if ($request->hasFile('collateral_images')) {
//             foreach ($request->file('collateral_images') as $image) {
//                 $collateralPaths[] = $image->store('collaterals', 'public');
//             }
//         }

//         $detail = LoanApplicationDetail::create([
//             'loan_application_id' => $request->loan_application_id,
//             'kin_name' => $request->kin_name,
//             'kin_contact' => $request->kin_contact,
//             'occupation' => $request->occupation,
//             'monthly_income' => $request->monthly_income,
//             'loan_type' => $request->loan_type,
//             'education' => $request->education,
//             'address' => $request->address,
//             'national_id_image' => $nationalIdPath,
//             'collateral_images' => $collateralPaths,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Loan application details submitted successfully',
//             'data' => $detail
//         ], 201);
//     }
// }
