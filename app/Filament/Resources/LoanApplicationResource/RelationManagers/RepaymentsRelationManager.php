<?php

namespace App\Filament\Resources\LoanApplicationResource\RelationManagers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RepaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'repayments';
    protected static ?string $title = 'Repayment Records';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')
                ->label('Amount Paid (UGX)')
                ->required()
                ->numeric()
                ->minValue(1),

            Select::make('payment_method')
                ->label('Payment Method')
                ->required()
                ->options([
                    'mobile_money'  => 'Mobile Money',
                    'cash'          => 'Cash',
                    'bank_transfer' => 'Bank Transfer',
                ])
                ->native(false),

            DatePicker::make('payment_date')
                ->label('Payment Date')
                ->required()
                ->default(now()),

            TextInput::make('reference_number')
                ->label('Reference Number')
                ->maxLength(100),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->maxLength(500),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount Paid')
                    ->money('UGX')
                    ->sortable()
                    ->color('success'),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'mobile_money'  => 'Mobile Money',
                        'cash'          => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        default         => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'mobile_money'  => 'info',
                        'cash'          => 'warning',
                        'bank_transfer' => 'primary',
                        default         => 'gray',
                    }),

                TextColumn::make('reference_number')
                    ->label('Reference #')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('loan_amount')
                    ->label('Total Loan')
                    ->money('UGX')
                    ->color('primary')
                    ->getStateUsing(fn ($record) =>
                        $record->loanApplication?->loan_amount ?? 0
                    ),

                TextColumn::make('total_repaid')
                    ->label('Total Repaid')
                    ->money('UGX')
                    ->color('success')
                    ->getStateUsing(fn ($record) =>
                        $record->loanApplication?->repayments()->sum('amount') ?? 0
                    ),

                TextColumn::make('balance')
                    ->label('Remaining Balance')
                    ->money('UGX')
                    ->getStateUsing(fn ($record) =>
                        ($record->loanApplication?->loan_amount ?? 0) -
                        ($record->loanApplication?->repayments()->sum('amount') ?? 0)
                    )
                    ->color(fn ($record) =>
                        (($record->loanApplication?->loan_amount ?? 0) -
                         ($record->loanApplication?->repayments()->sum('amount') ?? 0)) > 0
                            ? 'danger' : 'success'
                    ),
            ])
            ->defaultSort('payment_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Record Repayment')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id']     = $this->getOwnerRecord()->user_id;
                        $data['recorded_by'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
