<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\LoanApplication;
use App\Models\Repayment;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Return empty profile when not authenticated
        if (!$user) {
            return response()->json([
                'name'    => '',
                'email'   => '',
                'phone'   => '',
                'profile' => null,
            ]);
        }

        $user->load('profile');
        return response()->json([
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone ?? '',
            'profile' => $user->profile ? [
                'bio'             => $user->profile->bio ?? '',
                'address'         => $user->profile->address ?? '',
                'other_contact'   => $user->profile->other_contact ?? '',
                'kin_name'        => $user->profile->kin_name ?? '',
                'kin_contact'     => $user->profile->kin_contact ?? '',
                'income'          => $user->profile->income ?? '',
                'current_address' => $user->profile->current_address ?? '',
                'gender'          => $user->profile->gender ?? 'Other',
                'occupation'      => $user->profile->occupation ?? 'Other',
                'loan_type'       => $user->profile->loan_type ?? '',
                'education'       => $user->profile->education ?? '',
                'profile_image'   => $user->profile->profile_image
                    ? Storage::url($user->profile->profile_image) : null,
            ] : null,
        ]);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Return empty dashboard when not authenticated
        if (!$user) {
            return response()->json([
                'success' => true,
                'user'    => null,
                'summary' => [
                    'total_loans'    => 0,
                    'pending'        => 0,
                    'approved'       => 0,
                    'disbursed'      => 0,
                    'rejected'       => 0,
                    'repaying'       => 0,
                    'completed'      => 0,
                    'total_borrowed' => 0,
                    'total_repaid'   => 0,
                    'total_balance'  => 0,
                ],
                'recent_loans'      => [],
                'recent_repayments' => [],
            ]);
        }

        $loans = LoanApplication::where('user_id', $user->id)->latest()->get();

        $totalLoans     = $loans->count();
        $pendingLoans   = $loans->where('status', 'pending')->count();
        $approvedLoans  = $loans->where('status', 'approved')->count();
        $disbursedLoans = $loans->where('status', 'disbursed')->count();
        $rejectedLoans  = $loans->where('status', 'rejected')->count();
        $repayingLoans  = $loans->where('status', 'repaying')->count();
        $completedLoans = $loans->where('status', 'completed')->count();

        $totalBorrowed = $loans->whereIn('status', ['disbursed', 'repaying', 'completed'])->sum('loan_amount');
        $totalRepaid   = Repayment::where('user_id', $user->id)->sum('amount');
        $totalBalance  = $totalBorrowed - $totalRepaid;

        $recentLoans = $loans->take(5)->map(fn($app) => [
            'id'           => $app->id,
            'loan_type'    => $app->loan_type,
            'loan_amount'  => $app->loan_amount,
            'status'       => $app->status,
            'can_edit'     => $app->status === 'pending',
            'total_repaid' => $app->total_repaid,
            'balance'      => $app->balance,
            'due_date'     => $app->due_date,
            'created_at'   => $app->created_at,
        ]);

        $recentRepayments = Repayment::where('user_id', $user->id)
            ->with('loanApplication:id,loan_type')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'loan_type'      => $r->loanApplication->loan_type ?? '',
                'amount'         => $r->amount,
                'payment_method' => $r->payment_method,
                'payment_date'   => $r->payment_date,
            ]);

        return response()->json([
            'success' => true,
            'user' => [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'summary' => [
                'total_loans'    => $totalLoans,
                'pending'        => $pendingLoans,
                'approved'       => $approvedLoans,
                'disbursed'      => $disbursedLoans,
                'rejected'       => $rejectedLoans,
                'repaying'       => $repayingLoans,
                'completed'      => $completedLoans,
                'total_borrowed' => $totalBorrowed,
                'total_repaid'   => $totalRepaid,
                'total_balance'  => $totalBalance,
            ],
            'recent_loans'      => $recentLoans,
            'recent_repayments' => $recentRepayments,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'            => 'sometimes|string|max:255',
            'phone'           => 'sometimes|nullable|string|max:20',
            'bio'             => 'sometimes|nullable|string|max:500',
            'address'         => 'sometimes|nullable|string|max:255',
            'other_contact'   => 'sometimes|nullable|string|max:20',
            'kin_name'        => 'sometimes|nullable|string|max:255',
            'kin_contact'     => 'sometimes|nullable|string|max:20',
            'income'          => 'sometimes|nullable|string|max:50',
            'current_address' => 'sometimes|nullable|string|max:255',
            'gender'          => 'sometimes|nullable|string|max:20',
            'occupation'      => 'sometimes|nullable|string|max:100',
            'loan_type'       => 'sometimes|nullable|string|max:100',
            'education'       => 'sometimes|nullable|string|max:100',
            'profile_image'   => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'phone']));

        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            if ($user->profile?->profile_image) {
                Storage::disk('public')->delete($user->profile->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        $profileData = $request->only([
            'bio', 'address', 'other_contact', 'kin_name', 'kin_contact',
            'income', 'current_address', 'gender', 'occupation', 'loan_type', 'education',
        ]);

        if ($imagePath) $profileData['profile_image'] = $imagePath;

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return $this->show($request);
    }

    public function syncFromLoanApplication($user, array $loanData): void
    {
        $map = [
            'full_name'       => 'name',
            'phone'           => 'phone',
            'address'         => 'address',
            'other_contact'   => 'other_contact',
            'kin_name'        => 'kin_name',
            'kin_contact'     => 'kin_contact',
            'monthly_income'  => 'income',
            'current_address' => 'current_address',
            'gender'          => 'gender',
            'occupation'      => 'occupation',
            'loan_type'       => 'loan_type',
            'education'       => 'education',
        ];

        $userUpdate    = [];
        $profileUpdate = [];

        foreach ($map as $loanField => $profileField) {
            if (!isset($loanData[$loanField])) continue;
            if (in_array($profileField, ['name', 'phone'])) {
                $userUpdate[$profileField] = $loanData[$loanField];
            } else {
                $profileUpdate[$profileField] = $loanData[$loanField];
            }
        }

        if (!empty($userUpdate))    $user->update($userUpdate);
        if (!empty($profileUpdate)) {
            $user->profile()->updateOrCreate(['user_id' => $user->id], $profileUpdate);
        }
    }
}