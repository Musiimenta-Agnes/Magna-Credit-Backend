<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Validator;

class LoanApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'contact'             => 'required|string|max:50',
            'email'               => 'required|email|max:255',
            'bio_info'            => 'nullable|string',
            'location'            => 'required|string|max:255',
            'other_contact'       => 'nullable|string|max:50',
            'gender'              => 'required|string|in:Male,Female',
            'kin_name'            => 'required|string|max:255',
            'kin_contact'         => 'required|string|max:50',
            'occupation'          => 'required|string|max:255',
            'monthly_income'      => 'required|numeric|min:0',
            'loan_amount'         => 'required|numeric|min:0',
            'loan_type'           => 'required|string|max:255',
            'education'           => 'required|string|max:255',
            'address'             => 'required|string|max:255',
            'national_id_image'   => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'collateral_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $nationalIdPath = $request->file('national_id_image')->store('national_ids', 'public');
            $collateralPaths = [];
            if ($request->hasFile('collateral_images')) {
                foreach ($request->file('collateral_images') as $image) {
                    $collateralPaths[] = $image->store('collaterals', 'public');
                }
            }

            $loanApplication = LoanApplication::create([
                'user_id'           => $request->user()->id,
                'name'              => $request->name,
                'contact'           => $request->contact,
                'email'             => $request->email,
                'bio_info'          => $request->bio_info,
                'location'          => $request->location,
                'other_contact'     => $request->other_contact,
                'gender'            => $request->gender,
                'kin_name'          => $request->kin_name,
                'kin_contact'       => $request->kin_contact,
                'occupation'        => $request->occupation,
                'monthly_income'    => $request->monthly_income,
                'loan_amount'       => $request->loan_amount,
                'loan_type'         => $request->loan_type,
                'education'         => $request->education,
                'address'           => $request->address,
                'national_id_image' => $nationalIdPath,
                'collateral_images' => $collateralPaths,
                'status'            => 'pending',
            ]);

            if (class_exists(\App\Http\Controllers\Api\ProfileController::class)) {
                app(ProfileController::class)->syncFromLoanApplication($request->user(), [
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
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Loan application submitted successfully.', 'data' => $loanApplication], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $applications = LoanApplication::where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(function ($app) {
                return [
                    'id'                => $app->id,
                    'loan_type'         => $app->loan_type,
                    'loan_amount'       => $app->loan_amount,
                    'status'            => $app->status,
                    'monthly_income'    => $app->monthly_income,
                    'disbursement_date' => $app->disbursement_date,
                    'due_date'          => $app->due_date,
                    'rejection_reason'  => $app->rejection_reason,
                    'total_repaid'      => $app->total_repaid,
                    'balance'           => $app->balance,
                    'can_edit'          => $app->status === 'pending',
                    'created_at'        => $app->created_at,
                    // -- Full fields needed for edit form --
                    'name'              => $app->name,
                    'contact'           => $app->contact,
                    'email'             => $app->email,
                    'bio_info'          => $app->bio_info,
                    'location'          => $app->location,
                    'other_contact'     => $app->other_contact,
                    'gender'            => $app->gender,
                    'kin_name'          => $app->kin_name,
                    'kin_contact'       => $app->kin_contact,
                    'occupation'        => $app->occupation,
                    'education'         => $app->education,
                    'address'           => $app->address,
                ];
            });

        return response()->json(['success' => true, 'data' => $applications]);
    }

    public function show(Request $request, $id)
    {
        $application = LoanApplication::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => array_merge($application->toArray(), [
                'total_repaid' => $application->total_repaid,
                'balance'      => $application->balance,
                'can_edit'     => $application->status === 'pending',
            ]),
        ]);
    }

    public function update(Request $request, $id)
    {
        $application = LoanApplication::where('user_id', $request->user()->id)->findOrFail($id);

        if ($application->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This loan can no longer be edited. Status: ' . $application->status,
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'           => 'sometimes|nullable|string|max:255',
            'contact'        => 'sometimes|nullable|string|max:50',
            'email'          => 'sometimes|nullable|email|max:255',
            'bio_info'       => 'sometimes|nullable|string',
            'location'       => 'sometimes|nullable|string|max:255',
            'other_contact'  => 'sometimes|nullable|string|max:50',
            'gender'         => 'sometimes|nullable|string|max:20',
            'kin_name'       => 'sometimes|nullable|string|max:255',
            'kin_contact'    => 'sometimes|nullable|string|max:50',
            'occupation'     => 'sometimes|nullable|string|max:255',
            'monthly_income' => 'sometimes|nullable|numeric|min:0',
            'loan_amount'    => 'sometimes|nullable|numeric|min:0',
            'loan_type'      => 'sometimes|nullable|string|max:255',
            'education'      => 'sometimes|nullable|string|max:255',
            'address'        => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $application->update($request->only([
            'name', 'contact', 'email', 'bio_info', 'location',
            'other_contact', 'gender', 'kin_name', 'kin_contact',
            'occupation', 'monthly_income', 'loan_amount',
            'loan_type', 'education', 'address',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Loan application updated successfully.',
            'data'    => $application->fresh(),
        ]);
    }
}
