<?php

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use App\Models\User;
use Filament\Widgets\Widget;

class LoanStatsOverview extends Widget
{
    protected string $view = 'filament.widgets.loan-stats-overview';

    protected function getViewData(): array
    {
        $totalApplications = LoanApplication::count();
        $totalUsers        = User::count();
        $totalLoansAmount  = LoanApplication::whereIn('status', ['approved', 'disbursed'])->sum('loan_amount') ?? 0;
        $totalDisbursed    = LoanApplication::where('status', 'disbursed')->sum('loan_amount') ?? 0;
        $rejectedLoans     = LoanApplication::where('status', 'rejected')->count();
        $pendingLoans      = LoanApplication::where('status', 'pending')->count();
        $approvedLoans     = LoanApplication::where('status', 'approved')->count();

        return [
            'stats' => [
                ['label' => 'Total Applications', 'value' => number_format($totalApplications), 'description' => 'All submitted applications', 'icon' => 'document-text', 'color' => '#007BFF', 'bg' => '#EBF4FF'],
                ['label' => 'Registered Users', 'value' => number_format($totalUsers), 'description' => 'Active platform users', 'icon' => 'users', 'color' => '#28a745', 'bg' => '#EAFAF1'],
                ['label' => 'Pending Loans', 'value' => number_format($pendingLoans), 'description' => 'Awaiting review', 'icon' => 'clock', 'color' => '#FFC107', 'bg' => '#FFFBEB'],
                ['label' => 'Total Loans Value', 'value' => 'UGX ' . number_format($totalLoansAmount), 'description' => 'Approved & disbursed', 'icon' => 'banknotes', 'color' => '#007BFF', 'bg' => '#EBF4FF'],
                ['label' => 'Total Disbursed', 'value' => 'UGX ' . number_format($totalDisbursed), 'description' => 'Successfully paid out', 'icon' => 'arrow-trending-up', 'color' => '#28a745', 'bg' => '#EAFAF1'],
                ['label' => 'Rejected Loans', 'value' => number_format($rejectedLoans), 'description' => $approvedLoans . ' approved so far', 'icon' => 'x-circle', 'color' => '#dc3545', 'bg' => '#FEF2F2'],
            ],
        ];
    }
}
