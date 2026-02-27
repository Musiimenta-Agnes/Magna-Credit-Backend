<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    /**
     * Submit the full loan application (page 1 + page 2 combined).
     * Called once from Flutter page 2 with all fields + images.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // ── Page 1: Personal Information ──
            'name'                => 'required|string|max:255',
            'contact'             => 'required|string|max:50',
            'email'               => 'required|email|max:255',
            'bio_info'            => 'nullable|string',
            'location'            => 'required|string|max:255',
            'other_contact'       => 'nullable|string|max:50',
            'gender'              => 'required|string|in:Male,Female',

            // ── Page 2: Employment & Loan Details ──
            'kin_name'            => 'required|string|max:255',
            'kin_contact'         => 'required|string|max:50',
            'occupation'          => 'required|string|max:255',
            'monthly_income'      => 'required|numeric|min:0',
            'loan_amount'         => 'required|numeric|min:0',
            'loan_type'           => 'required|string|max:255',
            'education'           => 'required|string|max:255',
            'address'             => 'required|string|max:255',

            // ── Documents ──
            'national_id_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'collateral_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // ── Upload National ID ──
            $nationalIdPath = $request->file('national_id_image')
                                      ->store('national_ids', 'public');

            // ── Upload Collateral Images ──
            $collateralPaths = [];
            if ($request->hasFile('collateral_images')) {
                foreach ($request->file('collateral_images') as $image) {
                    $collateralPaths[] = $image->store('collaterals', 'public');
                }
            }

            // ── Save everything in one record ──
            $loanApplication = LoanApplication::create([
                // Page 1
                'user_id'           => $request->user()->id,  // from Sanctum token
                'name'              => $request->name,
                'contact'           => $request->contact,
                'email'             => $request->email,
                'bio_info'          => $request->bio_info,
                'location'          => $request->location,
                'other_contact'     => $request->other_contact,
                'gender'            => $request->gender,

                // Page 2
                'kin_name'          => $request->kin_name,
                'kin_contact'       => $request->kin_contact,
                'occupation'        => $request->occupation,
                'monthly_income'    => $request->monthly_income,
                'loan_amount'       => $request->loan_amount,
                'loan_type'         => $request->loan_type,
                'education'         => $request->education,
                'address'           => $request->address,

                // Documents
                'national_id_image' => $nationalIdPath,
                'collateral_images' => $collateralPaths,

                // Default status
                'status'            => 'pending',
            ]);

            // ── Sync to profile if ProfileController exists ──
            if (class_exists(\App\Http\Controllers\Api\ProfileController::class)) {
                app(ProfileController::class)->syncFromLoanApplication(
                    $request->user(),
                    [
                        'full_name'      => $request->name,
                        'phone'          => $request->contact,
                        'address'        => $request->location,
                        'other_contact'  => $request->other_contact,
                        'gender'         => $request->gender,
                        'kin_name'       => $request->kin_name,
                        'kin_contact'    => $request->kin_contact,
                        'occupation'     => $request->occupation,
                        'monthly_income' => $request->monthly_income,
                        'loan_type'      => $request->loan_type,
                        'education'      => $request->education,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Loan application submitted successfully.',
                'data'    => $loanApplication,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all loan applications for the authenticated user.
     */
    public function index(Request $request)
    {
        $applications = LoanApplication::where('user_id', $request->user()->id)
                                        ->latest()
                                        ->get();

        return response()->json([
            'success' => true,
            'data'    => $applications,
        ]);
    }

    /**
     * Get a single loan application.
     */
    public function show(Request $request, $id)
    {
        $application = LoanApplication::where('user_id', $request->user()->id)
                                       ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $application,
        ]);
    }
}