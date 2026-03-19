<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LoanApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanApplications';
    protected static ?string $title = 'Loan Applications';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Personal Details')
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
                    Textarea::make('bio_info')->label('Bio / About')->rows(2)->maxLength(1000),
                ]),

            Section::make('Next of Kin')
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('kin_name')->label('Next of Kin Name')->required()->maxLength(255),
                        TextInput::make('kin_contact')->label('Next of Kin Contact')->required()->tel()->maxLength(50),
                    ]),
                ]),

            Section::make('Loan Details')
                ->collapsible()
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('loan_type')->label('Loan Type')->required()
                            ->options([
                                'Logbook Loan'    => 'Logbook Loan',
                                'Business Loan'   => 'Business Loan',
                                'Personal Loan'   => 'Personal Loan',
                                'Asset Financing Loan' => 'Asset Financing Loan',
                                'Salary Loan'        => 'Salary Loan',
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

            Section::make('Status')
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
                    ]),
                ]),

            Section::make('Documents')
                ->collapsible()
                ->schema([
                    FileUpload::make('national_id_image')
                        ->label('National ID Image')
                        ->image()->disk('public')->directory('national_ids')
                        ->maxSize(2048)->columnSpanFull(),
                    FileUpload::make('collateral_images')
                        ->label('Collateral Images')
                        ->image()->multiple()->disk('public')->directory('collaterals')
                        ->maxSize(2048)->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan_type')->sortable()->label('Loan Type'),
                TextColumn::make('loan_amount')->money('UGX')->sortable()->label('Amount'),
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
                TextColumn::make('loan_amount')->label('Loan Amount')->money('UGX'),
                TextColumn::make('created_at')->date()->sortable()->label('Applied On'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Loan Application')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                Action::make('approve')->label('Approve')->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update([
                        'status' => 'approved', 'reviewed_by' => Auth::id(), 'reviewed_at' => now()
                    ]))
                    ->requiresConfirmation(),

                Action::make('reject')->label('Reject')->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([Textarea::make('rejection_reason')->required()->label('Reason')])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'rejected', 'rejection_reason' => $data['rejection_reason'],
                        'reviewed_by' => Auth::id(), 'reviewed_at' => now()
                    ]))
                    ->requiresConfirmation(),

                Action::make('disburse')->label('Disburse')->color('info')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->form([
                        DatePicker::make('disbursement_date')->required()->default(now())->label('Disbursement Date'),
                        DatePicker::make('due_date')->required()->label('Due Date'),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'disbursed',
                        'disbursement_date' => $data['disbursement_date'],
                        'due_date' => $data['due_date'],
                    ]))
                    ->requiresConfirmation(),

                EditAction::make(),
                DeleteAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }
}
