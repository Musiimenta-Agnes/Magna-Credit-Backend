<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Repayment;
use App\Models\LoanApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepaymentController extends Controller
{
    // GET /api/repayments - all repayments for the logged in user
    public function index(Request $request)
    {
        $repayments = Repayment::where('user_id', $request->user()->id)
            ->with('loanApplication:id,loan_type,loan_amount,status')
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'               => $r->id,
                'loan_id'          => $r->loan_application_id,
                'loan_type'        => $r->loanApplication->loan_type ?? '',
                'amount'           => $r->amount,
                'payment_method'   => $r->payment_method,
                'reference_number' => $r->reference_number,
                'payment_date'     => $r->payment_date,
                'notes'            => $r->notes,
                'created_at'       => $r->created_at,
            ]);

        return response()->json(['success' => true, 'data' => $repayments]);
    }

    // GET /api/repayments/{loanId} - repayments for a specific loan
    public function byLoan(Request $request, $loanId)
    {
        $loan = LoanApplication::where('user_id', $request->user()->id)->findOrFail($loanId);

        $repayments = Repayment::where('loan_application_id', $loanId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'loan' => [
                'id'           => $loan->id,
                'loan_type'    => $loan->loan_type,
                'loan_amount'  => $loan->loan_amount,
                'status'       => $loan->status,
                'total_repaid' => $loan->total_repaid,
                'balance'      => $loan->balance,
                'due_date'     => $loan->due_date,
            ],
            'repayments' => $repayments,
        ]);
    }

    // POST /api/repayments - make a repayment
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_application_id' => 'required|exists:loan_applications,id',
            'amount'              => 'required|numeric|min:1',
            'payment_method'      => 'required|in:cash,mobile_money,bank_transfer',
            'reference_number'    => 'nullable|string|max:100',
            'payment_date'        => 'required|date',
            'notes'               => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Make sure this loan belongs to the user
        $loan = LoanApplication::where('user_id', $request->user()->id)
            ->where('id', $request->loan_application_id)
            ->first();

        if (!$loan) {
            return response()->json(['success' => false, 'message' => 'Loan not found.'], 404);
        }

        // Only allow repayments on disbursed or repaying loans
        if (!in_array($loan->status, ['disbursed', 'repaying', 'approved'])) {
            return response()->json([
                'success' => false,
                'message' => 'Repayments can only be made on active loans.',
            ], 403);
        }

        $repayment = Repayment::create([
            'loan_application_id' => $request->loan_application_id,
            'user_id'             => $request->user()->id,
            'amount'              => $request->amount,
            'payment_method'      => $request->payment_method,
            'reference_number'    => $request->reference_number,
            'payment_date'        => $request->payment_date,
            'notes'               => $request->notes,
            'recorded_by'         => $request->user()->id,
        ]);

        // Update loan status to repaying if it was disbursed
        if ($loan->status === 'disbursed') {
            $loan->update(['status' => 'repaying']);
        }

        // Check if fully paid
        if ($loan->balance <= 0) {
            $loan->update(['status' => 'completed']);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Repayment recorded successfully.',
            'repayment' => $repayment,
            'loan' => [
                'total_repaid' => $loan->fresh()->total_repaid,
                'balance'      => $loan->fresh()->balance,
                'status'       => $loan->fresh()->status,
            ],
        ], 201);
    }
}
