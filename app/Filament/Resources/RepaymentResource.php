<?php
namespace App\Filament\Resources;
use App\Filament\Resources\RepaymentResource\Pages;
use App\Models\Repayment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RepaymentResource extends Resource
{
    protected static ?string $model = Repayment::class;

    public static function getNavigationLabel(): string { return 'Repayments'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): ?string { return 'Finance'; }
    public static function getNavigationSort(): ?int { return 1; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Repayment Details')
                ->schema([
                    Select::make('loan_application_id')
                        ->label('Loan Application')
                        ->relationship('loanApplication', 'name')
                        ->searchable()->required(),
                    Select::make('user_id')
                        ->label('Client')
                        ->relationship('user', 'name')
                        ->searchable()->required(),
                    TextInput::make('amount')->numeric()->prefix('UGX')->required(),
                    Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'mobile_money' => 'Mobile Money',
                            'bank_transfer' => 'Bank Transfer',
                        ])->required(),
                    TextInput::make('reference_number'),
                    DatePicker::make('payment_date')->required()->default(now()),
                    Hidden::make('recorded_by')->default(fn () => Auth::id()),
                    Textarea::make('notes')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('loanApplication.name')->label('Client')->searchable()->sortable(),
                TextColumn::make('amount')->money('UGX')->sortable(),
                TextColumn::make('payment_method')->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cash' => 'Cash',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Bank Transfer',
                        default => $state,
                    }),
                TextColumn::make('reference_number'),
                TextColumn::make('payment_date')->date()->sortable(),
                TextColumn::make('recordedBy.name')->label('Recorded By'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                EditAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
                DeleteAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRepayments::route('/'),
            'create' => Pages\CreateRepayment::route('/create'),
            'view'   => Pages\ViewRepayment::route('/{record}'),
            'edit'   => Pages\EditRepayment::route('/{record}/edit'),
        ];
    }
}













 
// namespace App\Filament\Resources;
// use App\Filament\Resources\RepaymentResource\Pages;
// use App\Models\Repayment;
// use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\Hidden;
// use Filament\Schemas\Components\Section;
// use Filament\Forms\Components\Select;
// use Filament\Forms\Components\Textarea;
// use Filament\Forms\Components\TextInput;
// use Filament\Resources\Resource;
// use Filament\Schemas\Schema;
// use Filament\Actions\EditAction;
// use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Table;
// use Illuminate\Support\Facades\Auth;

// class RepaymentResource extends Resource
// {
//     protected static ?string $model = Repayment::class;

//     public static function getNavigationLabel(): string { return 'Repayments'; }
//     public static function getNavigationIcon(): string { return 'heroicon-o-banknotes'; }
//     public static function getNavigationGroup(): ?string { return 'Finance'; }
//     public static function getNavigationSort(): ?int { return 1; }

//     public static function form(Schema $schema): Schema
//     {
//         return $schema->components([
//             Section::make('Repayment Details')
//                 ->schema([
//                     Select::make('loan_application_id')
//                         ->label('Loan Application')
//                         ->relationship('loanApplication', 'name')
//                         ->searchable()->required(),
//                     Select::make('user_id')
//                         ->label('Client')
//                         ->relationship('user', 'name')
//                         ->searchable()->required(),
//                     TextInput::make('amount')->numeric()->prefix('UGX')->required(),
//                     Select::make('payment_method')
//                         ->options([
//                             'cash' => 'Cash',
//                             'mobile_money' => 'Mobile Money',
//                             'bank_transfer' => 'Bank Transfer',
//                         ])->required(),
//                     TextInput::make('reference_number'),
//                     DatePicker::make('payment_date')->required()->default(now()),
//                     Hidden::make('recorded_by')->default(fn () => Auth::id()),
//                     Textarea::make('notes')->columnSpanFull(),
//                 ])->columns(2),
//         ]);
//     }

//     public static function table(Table $table): Table
//     {
//         return $table
//             ->columns([
//                 TextColumn::make('loanApplication.name')->label('Client')->searchable()->sortable(),
//                 TextColumn::make('amount')->money('UGX')->sortable(),
//                 TextColumn::make('payment_method')->badge()
//                     ->formatStateUsing(fn ($state) => match($state) {
//                         'cash' => 'Cash',
//                         'mobile_money' => 'Mobile Money',
//                         'bank_transfer' => 'Bank Transfer',
//                         default => $state,
//                     }),
//                 TextColumn::make('reference_number'),
//                 TextColumn::make('payment_date')->date()->sortable(),
//                 TextColumn::make('recordedBy.name')->label('Recorded By'),
//                 TextColumn::make('created_at')->dateTime()->sortable(),
//             ])
//             ->actions([
//                 EditAction::make()->visible(fn () => Auth::user()?->hasRole('super_admin')),
//             ]);
//     }

//     public static function getPages(): array
//     {
//         return [
//             'index'  => Pages\ListRepayments::route('/'),
//             'create' => Pages\CreateRepayment::route('/create'),
//             'view'   => Pages\ViewRepayment::route('/{record}'),
//             'edit'   => Pages\EditRepayment::route('/{record}/edit'),
//         ];
//     }
