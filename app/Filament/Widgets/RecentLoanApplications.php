<?php

namespace App\Filament\Widgets;

use App\Models\LoanApplication;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;

class RecentLoanApplications extends BaseTableWidget
{
    protected static ?string $heading = 'Recent Loan Applications';
    
    // Default column span to 1 (occupying half the dashboard on 2-column layouts)
    protected int | string | array $columnSpan = 1;
    
    // Set a lower sort order so it is clean
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoanApplication::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Applicant')
                    ->weight('bold')
                    ->description(fn (LoanApplication $record): string => $record->email ?? '')
                    ->fontFamily('Inter'),
                    
                // High density column merging Amount and Type
                Tables\Columns\TextColumn::make('loan_amount')
                    ->label('Amount')
                    ->money('UGX')
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn (LoanApplication $record): string => $record->loan_type ?? '')
                    ->fontFamily('Inter'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'disbursed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->fontFamily('Inter'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->since()
                    ->color('gray')
                    ->fontFamily('Inter'),
            ])
            ->paginated(false); // Clean feed, no pagination needed for a dashboard widget
    }
}
