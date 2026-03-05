<?php

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use App\Models\User;
use Filament\Widgets\ChartWidget;

class LoanStatusPieChart extends ChartWidget
{
    protected ?string $heading = 'Loan Applications by Status';
    protected ?string $maxHeight = '280px';
    protected string $color = 'info';

    protected function getType(): string { return 'bar'; }

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'Count',
                'data'  => [
                    LoanApplication::where('status', 'pending')->count(),
                    LoanApplication::where('status', 'approved')->count(),
                    LoanApplication::where('status', 'disbursed')->count(),
                    LoanApplication::where('status', 'rejected')->count(),
                    User::count(),
                ],
                'backgroundColor' => ['rgba(255,193,7,0.85)','rgba(0,123,255,0.85)','rgba(40,167,69,0.85)','rgba(220,53,69,0.85)','rgba(111,66,193,0.85)'],
                'borderColor'     => ['#e0a800','#0056b3','#1e7e34','#bd2130','#5a32a3'],
                'borderWidth'  => 2,
                'borderRadius' => 8,
                'borderSkipped' => false,
            ]],
            'labels' => ['Pending', 'Approved', 'Disbursed', 'Rejected', 'Users'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales'  => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1, 'font' => ['size' => 12]], 'grid' => ['color' => 'rgba(0,0,0,0.05)']],
                'x' => ['grid' => ['display' => false], 'ticks' => ['font' => ['size' => 12, 'weight' => '600']]],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
