<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string|Htmlable
    {
        return 'Welcome to Magna Credit Dashboard';
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
