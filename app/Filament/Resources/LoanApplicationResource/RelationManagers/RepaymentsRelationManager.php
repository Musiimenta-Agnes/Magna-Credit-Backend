<?php

namespace App\Filament\Resources\LoanApplicationResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RepaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'repayments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')->numeric()->prefix('UGX')->required(),
            Select::make('payment_method')
                ->options([
                    'cash' => 'Cash',
                    'mobile_money' => 'Mobile Money',
                    'bank_transfer' => 'Bank Transfer',
                ])->required(),
            TextInput::make('reference_number'),
            DatePicker::make('payment_date')->required()->default(now()),
            Hidden::make('user_id')
                ->default(fn ($livewire) => $livewire->ownerRecord->user_id),
            Hidden::make('recorded_by')
                ->default(fn () => Auth::id()),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->money('UGX'),
                TextColumn::make('payment_method'),
                TextColumn::make('reference_number'),
                TextColumn::make('payment_date')->date(),
                TextColumn::make('recordedBy.name')->label('Recorded By'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => Auth::user()?->hasPermissionTo('record repayments')),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
                DeleteAction::make()
                    ->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }
}