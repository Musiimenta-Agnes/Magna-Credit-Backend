<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string|Htmlable
    {
        $user = auth()->user();

        if ($user?->hasRole('super_admin')) {
            return 'Welcome back, Super Admin!';
        }

        if ($user?->hasRole('admin')) {
            return 'Welcome back, ' . $user->name . '!';
        }

        return 'Welcome back, ' . ($user?->name ?? 'Loans Officer') . '!';
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