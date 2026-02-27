<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplicationDetail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ProfileController;


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
            'loan_amount'         => 'required|numeric',
            'loan_type'           => 'required|string',
            'education'           => 'required|string',
            'address'             => 'required|string',
            'national_id_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'collateral_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Validation check comes FIRST before anything else
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

        // Save loan application details
        $detail = LoanApplicationDetail::create([
            'loan_application_id' => $request->loan_application_id,
            'kin_name'            => $request->kin_name,
            'kin_contact'         => $request->kin_contact,
            'occupation'          => $request->occupation,
            'monthly_income'      => $request->monthly_income,
            'loan_amount'         => $request->loan_amount,
            'loan_type'           => $request->loan_type,
            'education'           => $request->education,
            'address'             => $request->address,
            'national_id_image'   => $nationalIdPath,
            'collateral_images'   => $collateralPaths,
        ]);

        // ── Sync deeper profile info to profile page ──
        // Runs AFTER save and AFTER validation passes
        app(ProfileController::class)->syncFromLoanApplication(
            $request->user(),
            [
                'kin_name'       => $request->kin_name,
                'kin_contact'    => $request->kin_contact,
                'occupation'     => $request->occupation,
                'monthly_income' => $request->monthly_income,
                'loan_type'      => $request->loan_type,
                'education'      => $request->education,
                'address'        => $request->address,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Loan application details submitted successfully',
            'data'    => $detail
        ], 201);
    }
}









// class LoanApplicationDetailController extends Controller
// {
//     public function store(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'loan_application_id' => 'required|exists:loan_applications,id',
//             'kin_name'            => 'required|string',
//             'kin_contact'         => 'required|string',
//             'occupation'          => 'required|string',
//             'monthly_income'      => 'required|numeric',
//             'loan_amount'         => 'required|numeric',   // ← NEW
//             'loan_type'           => 'required|string',
//             'education'           => 'required|string',
//             'address'             => 'required|string',
//             'national_id_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
//             'collateral_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
//         ]);

//         // ── ADD THIS after you save the loan application ──
//         app(ProfileController::class)->syncFromLoanApplication($request->user(), $validated);

//         if ($validator->fails()) {
//             return response()->json([
//                 'success' => false,
//                 'errors'  => $validator->errors()
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
//             'kin_name'            => $request->kin_name,
//             'kin_contact'         => $request->kin_contact,
//             'occupation'          => $request->occupation,
//             'monthly_income'      => $request->monthly_income,
//             'loan_amount'         => $request->loan_amount,   // ← NEW
//             'loan_type'           => $request->loan_type,
//             'education'           => $request->education,
//             'address'             => $request->address,
//             'national_id_image'   => $nationalIdPath,
//             'collateral_images'   => $collateralPaths,
//         ]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Loan application details submitted successfully',
//             'data'    => $detail
//         ], 201);
//     }
// }

