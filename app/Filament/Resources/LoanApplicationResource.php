<?php
namespace App\Filament\Resources;
use App\Filament\Resources\LoanApplicationResource\Pages;
use App\Models\LoanApplication;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;

    public static function getNavigationLabel(): string { return 'All Applications'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): ?string { return 'Loan Management'; }
    public static function getNavigationSort(): ?int { return 1; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal Information')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('contact')->required(),
                    TextInput::make('email')->email()->required(),
                    Select::make('gender')
                        ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                    TextInput::make('location'),
                    TextInput::make('other_contact'),
                    Textarea::make('bio_info')->columnSpanFull(),
                ])->columns(2),

            Section::make('Next of Kin')
                ->schema([
                    TextInput::make('kin_name'),
                    TextInput::make('kin_contact'),
                ])->columns(2),

            Section::make('Loan Details')
                ->schema([
                    TextInput::make('loan_type')->required(),
                    TextInput::make('loan_amount')->numeric()->prefix('UGX')->required()
                        ->disabled(fn () => !Auth::user()?->hasRole('super_admin')),
                    TextInput::make('monthly_income')->numeric()->prefix('UGX'),
                    TextInput::make('occupation'),
                    TextInput::make('education'),
                    TextInput::make('address'),
                ])->columns(2),

            Section::make('Loan Status')
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending', 'approved' => 'Approved',
                            'rejected' => 'Rejected', 'disbursed' => 'Disbursed',
                            'repaying' => 'Repaying', 'completed' => 'Completed',
                            'defaulted' => 'Defaulted',
                        ])->required()->disabled(fn () => !Auth::user()?->hasRole('super_admin')),
                    DatePicker::make('disbursement_date')->disabled(fn () => !Auth::user()?->hasRole('super_admin')),
                    DatePicker::make('due_date')->disabled(fn () => !Auth::user()?->hasRole('super_admin')),
                    Textarea::make('rejection_reason')->columnSpanFull()
                        ->disabled(fn () => !Auth::user()?->hasRole('super_admin')),
                ])->columns(2),

            Section::make('Identity & Collateral Documents')
                ->description('Documents submitted by the applicant via the mobile app.')
                ->schema([
                    ViewField::make('documents_viewer')
                        ->view('filament.components.loan-images')
                        ->columnSpanFull(),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved', 'disbursed', 'completed' => 'success',
                        'rejected', 'defaulted' => 'danger',
                        'repaying' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending', 'approved' => 'Approved',
                    'rejected' => 'Rejected', 'disbursed' => 'Disbursed',
                    'repaying' => 'Repaying', 'completed' => 'Completed',
                    'defaulted' => 'Defaulted',
                ]),
            ])
            ->actions([
                EditAction::make()->visible(fn () => Auth::user()?->hasPermissionTo('review loan applications')),
                Action::make('approve')->label('Approve')->color('success')
                    ->visible(fn ($record) => Auth::user()?->hasPermissionTo('approve loans') && $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]))
                    ->requiresConfirmation(),
                Action::make('reject')->label('Reject')->color('danger')
                    ->visible(fn ($record) => Auth::user()?->hasPermissionTo('reject loans') && $record->status === 'pending')
                    ->form([Textarea::make('rejection_reason')->required()])
                    ->action(fn ($record, array $data) => $record->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'], 'reviewed_by' => Auth::id(), 'reviewed_at' => now()]))
                    ->requiresConfirmation(),
                Action::make('disburse')->label('Disburse')->color('info')
                    ->visible(fn ($record) => Auth::user()?->hasPermissionTo('disburse loans') && $record->status === 'approved')
                    ->form([DatePicker::make('disbursement_date')->required()->default(now()), DatePicker::make('due_date')->required()])
                    ->action(fn ($record, array $data) => $record->update(['status' => 'disbursed', 'disbursement_date' => $data['disbursement_date'], 'due_date' => $data['due_date']]))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LoanApplicationResource\RelationManagers\RepaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLoanApplications::route('/'),
            'create' => Pages\CreateLoanApplication::route('/create'),
            'view'   => Pages\ViewLoanApplication::route('/{record}'),
            'edit'   => Pages\EditLoanApplication::route('/{record}/edit'),
        ];
    }
}
