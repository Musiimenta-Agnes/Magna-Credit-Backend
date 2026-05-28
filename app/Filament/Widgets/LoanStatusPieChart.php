<?php

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use Filament\Widgets\ChartWidget;

class LoanStatusPieChart extends ChartWidget
{
    protected ?string $heading = 'Loan Applications by Status';
    protected ?string $maxHeight = '280px';
    protected string $color = 'info';

    // We make it a full, solid pie chart!
    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'Applications',
                'data'  => [
                    LoanApplication::where('status', 'pending')->count(),
                    LoanApplication::where('status', 'approved')->count(),
                    LoanApplication::where('status', 'disbursed')->count(),
                    LoanApplication::where('status', 'rejected')->count(),
                ],
                'backgroundColor' => [
                    'rgba(56, 189, 248, 0.85)',  // Cyan / Lighter Blue (Pending)
                    'rgba(0, 118, 214, 0.85)',   // Premium Blue (Approved)
                    'rgba(40, 167, 69, 0.85)',    // Premium Green (Disbursed)
                    'rgba(100, 116, 139, 0.85)',  // Slate Gray (Rejected) - avoiding excessive red
                ],
                'borderColor' => [
                    '#38BDF8',
                    '#0076D6',
                    '#1e7e34',
                    '#64748B',
                ],
                'borderWidth' => 2,
                'hoverOffset' => 4,
            ]],
            'labels' => ['Pending', 'Approved', 'Disbursed', 'Rejected'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'weight' => '600',
                            'family' => 'Inter, sans-serif',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'padding' => 12,
                    'cornerRadius' => 10,
                    'titleFont' => ['size' => 12, 'family' => 'Inter, sans-serif'],
                    'bodyFont' => ['size' => 12, 'family' => 'Inter, sans-serif'],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
