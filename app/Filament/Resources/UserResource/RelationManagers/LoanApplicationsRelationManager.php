<?php
namespace App\Filament\Resources\UserResource\RelationManagers;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LoanApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'loanApplications';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Client Details')
                ->description('Auto-filled from the client record. Edit if needed.')
                ->schema([
                    TextInput::make('name')
                        ->default(fn () => $this->getOwnerRecord()->name)
                        ->required(),
                    TextInput::make('email')
                        ->email()
                        ->default(fn () => $this->getOwnerRecord()->email)
                        ->required(),
                    TextInput::make('contact')
                        ->default(fn () => $this->getOwnerRecord()->phone)
                        ->required(),
                    TextInput::make('location'),
                    TextInput::make('other_contact'),
                    TextInput::make('occupation'),
                    Select::make('gender')
                        ->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']),
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
                    TextInput::make('loan_amount')->numeric()->prefix('UGX')->required(),
                    TextInput::make('monthly_income')->numeric()->prefix('UGX'),
                    TextInput::make('education'),
                    TextInput::make('address'),
                    Select::make('status')
                        ->options([
                            'pending'   => 'Pending',
                            'approved'  => 'Approved',
                            'rejected'  => 'Rejected',
                            'disbursed' => 'Disbursed',
                            'repaying'  => 'Repaying',
                            'completed' => 'Completed',
                            'defaulted' => 'Defaulted',
                        ])->required()->default('pending'),
                    DatePicker::make('disbursement_date'),
                    DatePicker::make('due_date'),
                    Textarea::make('rejection_reason')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'approved', 'disbursed', 'completed' => 'success',
                        'rejected', 'defaulted' => 'danger',
                        'repaying'  => 'info',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Ensure name/email/contact are always filled from owner if empty
                        $owner = $this->getOwnerRecord();
                        $data['name']    = $data['name']    ?? $owner->name;
                        $data['email']   = $data['email']   ?? $owner->email;
                        $data['contact'] = $data['contact'] ?? $owner->phone;
                        $data['status']  = $data['status']  ?? 'pending';
                        return $data;
                    })
                    ->visible(fn () => Auth::user()?->hasPermissionTo('create clients')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => Auth::user()?->hasPermissionTo('review loan applications')),
                DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }
}
