<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanApplicationResource\Pages;
use App\Filament\Resources\LoanApplicationResource\RelationManagers\RepaymentsRelationManager;
use App\Models\LoanApplication;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LoanApplicationResource extends Resource
{
    protected static ?string $model = LoanApplication::class;
    protected static ?string $slug = 'loan-applications';
    protected static ?string $modelLabel = 'Loan Application';
    protected static ?string $pluralModelLabel = 'All Applications';

    public static function getNavigationLabel(): string { return 'All Applications'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): string { return 'Loan Management'; }
    public static function getNavigationSort(): int { return 0; }

    // ── loans_officer can ACCESS (see sidebar) but cannot do anything else ──
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string { return 'gray'; }

    public static function form(Schema $schema): Schema
    {
        $isCreate = $schema->getLivewire() instanceof \Filament\Resources\Pages\CreateRecord;

        return $schema->components([

            Section::make('Link to Existing Client (Optional)')
                ->icon('heroicon-o-user-circle')
                ->collapsible()
                ->collapsed()
                ->visible($isCreate)
                ->schema([
                    Select::make('user_id')
                        ->label('Link to Client Account')
                        ->options(User::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->placeholder('Leave blank for walk-in / new customer')
                        ->helperText('Only select if this person already has an account in the system.'),
                ]),

            Section::make('Personal Details')
                ->icon('heroicon-o-user')
                ->collapsible()
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
                ->icon('heroicon-o-users')
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('kin_name')->label('Next of Kin Name')->required()->maxLength(255),
                        TextInput::make('kin_contact')->label('Next of Kin Contact')->required()->tel()->maxLength(50),
                    ]),
                ]),

            Section::make('Loan Details')
                ->icon('heroicon-o-banknotes')
                ->collapsible()
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
                ->icon('heroicon-o-clipboard-document-check')
                ->collapsible()
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
                            ])
                            ->default('pending')
                            ->native(false),
                        DatePicker::make('disbursement_date')->label('Disbursement Date'),
                        DatePicker::make('due_date')->label('Due Date'),
                        TextInput::make('rejection_reason')->label('Rejection Reason')->maxLength(500)
                            ->visible(fn ($get) => $get('status') === 'rejected'),
                    ]),
                ]),

            Section::make('Documents')
                ->icon('heroicon-o-photo')
                ->collapsible()
                ->schema([
                    FileUpload::make('national_id_image')
                        ->label('National ID Image')
                        ->image()->disk('public')->directory('national_ids')
                        ->maxSize(2048)->columnSpanFull(),
                    FileUpload::make('collateral_images')
                        ->label('Collateral Images')
                        ->image()->multiple()->disk('public')->directory('collaterals')
                        ->maxSize(2048)->columnSpanFull()
                        ->helperText('Upload one or more collateral images.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = fn () => Auth::user()?->hasAnyRole(['super_admin', 'admin']);

        return $table
            ->defaultSort('created_at', 'desc')
            // loans_officer clicks row → view page; admin/super_admin → edit page
            ->recordUrl(fn ($record) => $isAdmin()
                ? static::getUrl('edit', ['record' => $record])
                : static::getUrl('view', ['record' => $record])
            )
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('contact')->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'approved'  => 'success',
                        'disbursed' => 'info',
                        'repaying'  => 'primary',
                        'completed' => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Applied'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $isAdmin() && $record->status === 'pending')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Approve Loan')
                    ->modalDescription('Are you sure you want to approve this loan application?'),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $isAdmin() && $record->status === 'pending')
                    ->form([Textarea::make('rejection_reason')->required()->label('Reason for Rejection')])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'rejected', 'rejection_reason' => $data['rejection_reason'],
                        'reviewed_by' => Auth::id(), 'reviewed_at' => now()
                    ]))
                    ->requiresConfirmation(),

                Action::make('disburse')
                    ->label('Disburse')
                    ->color('info')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn ($record) => $isAdmin() && $record->status === 'approved')
                    ->form([
                        DatePicker::make('disbursement_date')->required()->default(now())->label('Disbursement Date'),
                        DatePicker::make('due_date')->required()->label('Due Date'),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'disbursed',
                        'disbursement_date' => $data['disbursement_date'],
                        'due_date' => $data['due_date'],
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Disburse Loan')
                    ->modalDescription('Confirm disbursement details before proceeding.'),

                EditAction::make()
                    ->visible(fn () => $isAdmin()),

                DeleteAction::make()
                    ->visible(fn () => $isAdmin()),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => $isAdmin()),
            ]);
    }

    public static function getRelations(): array
    {
        return [RepaymentsRelationManager::class];
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