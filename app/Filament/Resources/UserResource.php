<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string { return 'Clients'; }
    public static function getNavigationIcon(): string { return 'heroicon-o-users'; }
    public static function getNavigationSort(): ?int { return 1; }

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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Client Information')
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    TextInput::make('phone')->required()->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = fn () => Auth::user()?->hasAnyRole(['super_admin', 'admin']);

        return $table
            ->defaultSort('created_at', 'desc')
            // exclude all admin-role users from the clients list
            ->modifyQueryUsing(fn ($query) => $query->whereDoesntHave('roles', fn ($q) =>
                $q->whereIn('name', ['super_admin', 'admin', 'loans_officer'])
            ))
            ->recordUrl(fn ($record) => $isAdmin()
                ? static::getUrl('edit', ['record' => $record])
                : static::getUrl('view', ['record' => $record])
            )
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('loanApplications_count')
                    ->counts('loanApplications')
                    ->label('Loan Applications'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
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
        return [
            UserResource\RelationManagers\LoanApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}