<?php
namespace App\Filament\Resources;
use App\Models\LoanApplication;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
class ApprovedLoanResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?string $slug = 'approved-loans';
    protected static ?string $modelLabel = 'Approved Loan';
    protected static ?string $pluralModelLabel = 'Approved Loans';
    public static function getNavigationLabel(): string { return 'Approved Loans'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-check-badge'; }
    public static function getNavigationGroup(): string { return 'Loan Management'; }
    public static function getNavigationSort(): int { return 2; }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'approved');
    }
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }
    public static function getNavigationBadgeColor(): string
    {
        return 'success';
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
                TextColumn::make('reviewed_at')->dateTime()->sortable()->label('Approved On'),
            ])
            ->actions([
                Action::make('disburse')
                    ->label('Disburse')
                    ->color('info')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn () => Auth::user()?->hasRole('super_admin'))
                    ->form([
                        DatePicker::make('disbursement_date')->required()->default(now())->label('Disbursement Date'),
                        DatePicker::make('due_date')->required()->label('Due Date'),
                    ])
                    ->action(fn ($record, array $data) => $record->update(['status' => 'disbursed', 'disbursement_date' => $data['disbursement_date'], 'due_date' => $data['due_date']]))
                    ->requiresConfirmation()
                    ->modalHeading('Disburse Loan')
                    ->modalDescription('Confirm disbursement details before proceeding.'),
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
            'index'  => \App\Filament\Resources\ApprovedLoanResource\Pages\ListApprovedLoans::route('/'),
            'create' => \App\Filament\Resources\ApprovedLoanResource\Pages\CreateApprovedLoan::route('/create'),
            'edit'   => \App\Filament\Resources\ApprovedLoanResource\Pages\EditApprovedLoan::route('/{record}/edit'),
        ];
    }
}