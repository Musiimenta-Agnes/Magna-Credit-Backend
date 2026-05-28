<?php

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoanStatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalApplications = LoanApplication::count();
        $totalUsers        = User::count();
        $totalLoansAmount  = LoanApplication::whereIn('status', ['approved', 'disbursed'])->sum('loan_amount') ?? 0;
        $totalDisbursed    = LoanApplication::where('status', 'disbursed')->sum('loan_amount') ?? 0;
        $rejectedLoans     = LoanApplication::where('status', 'rejected')->count();
        $pendingLoans      = LoanApplication::where('status', 'pending')->count();

        $appsTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return LoanApplication::whereDate('created_at', now()->subDays($daysAgo))->count();
        })->toArray();

        $usersTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return User::whereDate('created_at', now()->subDays($daysAgo))->count();
        })->toArray();

        $pendingTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return LoanApplication::where('status', 'pending')
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->count();
        })->toArray();

        $valueTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return LoanApplication::whereIn('status', ['approved', 'disbursed'])
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->sum('loan_amount');
        })->toArray();

        $disbursedTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return LoanApplication::where('status', 'disbursed')
                ->whereDate('disbursement_date', now()->subDays($daysAgo))
                ->sum('loan_amount');
        })->toArray();

        $rejectedTrend = collect(range(6, 0))->map(function ($daysAgo) {
            return LoanApplication::where('status', 'rejected')
                ->whereDate('created_at', now()->subDays($daysAgo))
                ->count();
        })->toArray();

        return [
            Stat::make('Total Applications', number_format($totalApplications))
                ->description('All submitted applications')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($appsTrend)
                ->color('info'),

            Stat::make('Registered Users', number_format($totalUsers))
                ->description('Active platform users')
                ->descriptionIcon('heroicon-m-users')
                ->chart($usersTrend)
                ->color('info'),

            Stat::make('Pending Loans', number_format($pendingLoans))
                ->description('Awaiting staff review')
                ->descriptionIcon('heroicon-m-clock')
                ->chart($pendingTrend)
                ->color('info'),

            Stat::make('Total Loans Value', 'UGX ' . number_format($totalLoansAmount))
                ->description('Approved & disbursed')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($valueTrend)
                ->color('info'),

            Stat::make('Total Disbursed', 'UGX ' . number_format($totalDisbursed))
                ->description('Paid out to clients')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($disbursedTrend)
                ->color('info'),

            Stat::make('Rejected Loans', number_format($rejectedLoans))
                ->description('Applications denied')
                ->descriptionIcon('heroicon-m-x-circle')
                ->chart($rejectedTrend)
                ->color('danger'),
        ];
    }
}