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
            Section::make('Loan Details')
                ->schema([
                    TextInput::make('loan_type')->required(),
                    TextInput::make('loan_amount')->numeric()->prefix('UGX')->required(),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'disbursed' => 'Disbursed',
                            'repaying' => 'Repaying',
                            'completed' => 'Completed',
                            'defaulted' => 'Defaulted',
                        ])->required(),
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
                TextColumn::make('loan_type')->sortable(),
                TextColumn::make('loan_amount')->money('UGX')->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
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