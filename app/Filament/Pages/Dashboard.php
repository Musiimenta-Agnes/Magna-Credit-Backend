<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string|Htmlable
    {
        if (auth()->user()?->hasRole('super_admin')) {
            return 'Welcome to the dashboard, Super Admin!';
        }
        return 'Welcome to the dashboard, Loans Officer!';
    }
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\LoanStatsOverview::class,
            \App\Filament\Widgets\LoanStatusPieChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
