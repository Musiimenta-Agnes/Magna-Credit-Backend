<?php

namespace App\Filament\Resources;

use App\Models\LoanApplication;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PendingLoanResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?string $slug = 'pending-loans';
    protected static ?string $modelLabel = 'Pending Loan';
    protected static ?string $pluralModelLabel = 'Pending Loans';

    public static function getNavigationLabel(): string { return 'Pending Loans'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): string { return 'Loan Management'; }
    public static function getNavigationSort(): int { return 1; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'pending');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return app(LoanApplicationResource::class)::form($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('monthly_income')->money('UGX')->label('Monthly Income'),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Applied'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn () => Auth::user()?->hasRole('super_admin'))
                    ->action(fn ($record) => $record->update(['status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Loan')
                    ->modalDescription('Are you sure you want to approve this loan application?'),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn () => Auth::user()?->hasRole('super_admin'))
                    ->form([Textarea::make('rejection_reason')->required()->label('Reason for Rejection')])
                    ->action(fn ($record, array $data) => $record->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'], 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]))
                    ->requiresConfirmation(),
                EditAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
                DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\LoanApplicationResource\RelationManagers\RepaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\PendingLoanResource\Pages\ListPendingLoans::route('/'),
            'create' => \App\Filament\Resources\PendingLoanResource\Pages\CreatePendingLoan::route('/create'),
            'edit'   => \App\Filament\Resources\PendingLoanResource\Pages\EditPendingLoan::route('/{record}/edit'),
        ];
    }
}