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

class RejectedLoanResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'rejected-loans';
    protected static ?string $modelLabel = 'Rejected Loan';
    protected static ?string $pluralModelLabel = 'Rejected Loans';

    public static function getNavigationLabel(): string { return 'Rejected Loans'; }
    public static function getNavigationGroup(): ?string { return 'Loan Management'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-x-circle'; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'rejected');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string { return 'danger'; }

    public static function form(Schema $schema): Schema
    {
        return LoanApplicationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('rejection_reason')->limit(50)->label('Reason'),
                TextColumn::make('reviewed_at')->dateTime()->sortable()->label('Rejected On'),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\RejectedLoanResource\Pages\ListRejectedLoans::route('/'),
            'create' => \App\Filament\Resources\RejectedLoanResource\Pages\CreateRejectedLoan::route('/create'),
            'edit'   => \App\Filament\Resources\RejectedLoanResource\Pages\EditRejectedLoan::route('/{record}/edit'),
        ];
    }
}
