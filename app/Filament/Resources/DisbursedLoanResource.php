<?php

namespace App\Filament\Resources;

use App\Models\LoanApplication;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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

    public static function canAccess(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin', 'loans_officer']) ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canView($record): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin', 'loans_officer']) ?? false;
    }

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
        return $schema->components([
            Section::make('Link to Existing Client (Optional)')
                ->icon('heroicon-o-user-circle')
                ->collapsible()->collapsed()
                ->schema([
                    Select::make('user_id')
                        ->label('Link to Client Account')
                        ->options(User::orderBy('name')->pluck('name', 'id'))
                        ->searchable()->nullable()
                        ->placeholder('Leave blank for walk-in / new customer'),
                ]),

            Section::make('Personal Details')
                ->icon('heroicon-o-user')->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label('Full Name')->required()->maxLength(255),
                        TextInput::make('contact')->label('Phone Number')->required()->tel()->maxLength(50),
                        TextInput::make('email')->label('Email Address')->required()->email()->maxLength(255),
                        TextInput::make('other_contact')->label('Other Contact')->tel()->maxLength(50),
                        TextInput::make('location')->label('Location')->required()->maxLength(255),
                        Select::make('gender')->label('Gender')->required()
                            ->options(['Male' => 'Male', 'Female' => 'Female'])->native(false),
                        TextInput::make('education')->label('Highest Education')->required()->maxLength(255),
                        TextInput::make('address')->label('Current Address')->required()->maxLength(255),
                    ]),
                    Textarea::make('bio_info')->label('Bio / About')->rows(3)->maxLength(1000),
                ]),

            Section::make('Next of Kin')
                ->icon('heroicon-o-users')->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('kin_name')->label('Next of Kin Name')->required()->maxLength(255),
                        TextInput::make('kin_contact')->label('Next of Kin Contact')->required()->tel()->maxLength(50),
                    ]),
                ]),

            Section::make('Loan Details')
                ->icon('heroicon-o-banknotes')->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('loan_type')->label('Loan Type')->required()
                            ->options([
                                'Logbook Loan'         => 'Logbook Loan',
                                'Business Loan'        => 'Business Loan',
                                'Personal Loan'        => 'Personal Loan',
                                'Asset Financing Loan' => 'Asset Financing Loan',
                                'Salary Loan'          => 'Salary Loan',
                            ])->native(false),
                        TextInput::make('loan_amount')->label('Loan Amount (UGX)')->required()->numeric()->minValue(0),
                        TextInput::make('monthly_income')->label('Monthly Income (UGX)')->required()->numeric()->minValue(0),
                        Select::make('occupation')->label('Occupation')->required()
                            ->options([
                                'Farmer' => 'Farmer', 'Business Owner' => 'Business Owner',
                                'Teacher' => 'Teacher', 'Engineer' => 'Engineer',
                                'Driver' => 'Driver', 'Student' => 'Student',
                                'Civil Servant' => 'Civil Servant', 'Medical Worker' => 'Medical Worker',
                                'Technician' => 'Technician', 'Other' => 'Other',
                            ])->native(false),
                    ]),
                ]),

            Section::make('Loan Status')
                ->icon('heroicon-o-clipboard-document-check')->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('status')->label('Status')->required()
                            ->options([
                                'pending'   => 'Pending',
                                'approved'  => 'Approved',
                                'disbursed' => 'Disbursed',
                                'repaying'  => 'Repaying',
                                'completed' => 'Completed',
                                'rejected'  => 'Rejected',
                            ])->default('disbursed')->native(false),
                        DatePicker::make('disbursement_date')->label('Disbursement Date'),
                        DatePicker::make('due_date')->label('Due Date'),
                        TextInput::make('rejection_reason')->label('Rejection Reason')->maxLength(500)
                            ->visible(fn ($get) => $get('status') === 'rejected'),
                    ]),
                ]),

            Section::make('Documents')
                ->icon('heroicon-o-photo')->collapsible()
                ->schema([
                    FileUpload::make('national_id_image')->label('National ID Image')
                        ->image()->disk('public')->directory('national_ids')->maxSize(2048)->columnSpanFull(),
                    FileUpload::make('collateral_images')->label('Collateral Images')
                        ->image()->multiple()->disk('public')->directory('collaterals')->maxSize(2048)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = fn () => Auth::user()?->hasAnyRole(['super_admin', 'admin']);

        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn ($record) => $isAdmin()
                ? static::getUrl('edit', ['record' => $record])
                : static::getUrl('view', ['record' => $record])
            )
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('disbursement_date')->date()->sortable()->label('Disbursed On'),
                TextColumn::make('due_date')->date()->sortable()->label('Due Date'),
            ])
            ->actions([
                EditAction::make()->visible(fn () => $isAdmin()),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => $isAdmin()),
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
