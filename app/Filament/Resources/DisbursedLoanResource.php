<?php

namespace App\Filament\Resources;

use App\Models\LoanApplication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DisbursedLoanResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?string $slug = 'disbursed-loans';
    protected static ?string $modelLabel = 'Disbursed Loan';
    protected static ?string $pluralModelLabel = 'Disbursed Loans';

    public static function getNavigationLabel(): string { return 'Disbursed Loans'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): string { return 'Loan Management'; }
    public static function getNavigationSort(): int { return 3; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'disbursed');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return app(LoanApplicationResource::class)::form($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => auth()->user()?->hasRole('super_admin') ? static::getUrl('edit', ['record' => $record]) : static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('disbursement_date')->date()->sortable()->label('Disbursed On'),
                TextColumn::make('due_date')->date()->sortable()->label('Due Date'),
            ])
            ->actions([
                EditAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
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
            'index'  => \App\Filament\Resources\DisbursedLoanResource\Pages\ListDisbursedLoans::route('/'),
            'create' => \App\Filament\Resources\DisbursedLoanResource\Pages\CreateDisbursedLoan::route('/create'),
            'view'   => \App\Filament\Resources\DisbursedLoanResource\Pages\ViewDisbursedLoan::route('/{record}'),
            'edit'   => \App\Filament\Resources\DisbursedLoanResource\Pages\EditDisbursedLoan::route('/{record}/edit'),
        ];
    }
}
