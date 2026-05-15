<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Models\User;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationLabel(): string
    {
        return 'Admin Users';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canDelete($record): bool
    {
        if ($record->id === Auth::id()) return false;
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canView($record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Admin Account Details')
                ->description('Set the admin login credentials and personal information.')
                ->schema([
                    TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->unique(table: User::class, column: 'email', ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->unique(table: User::class, column: 'phone', ignoreRecord: true)
                        ->maxLength(20),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->minLength(8)
                        ->hint(fn (string $context) => $context === 'edit'
                            ? 'Leave blank to keep the current password'
                            : null),
                ])->columns(2),

            Section::make('Role & Permissions')
                ->description('Assign a role to control what this admin can see and do.')
                ->schema([
                    Select::make('roles')
                        ->label('Dashboard Role')
                        ->relationship('roles', 'name')
                        ->options(
                            Role::whereIn('name', ['admin', 'loans_officer', 'super_admin'])
                                ->pluck('name', 'id')
                                ->map(fn ($name) => match ($name) {
                                    'super_admin'   => 'Super Admin - Full access to everything',
                                    'admin'         => 'Admin - Manage loans, repayments, clients',
                                    'loans_officer' => 'Loans Officer - View only access',
                                    default         => $name,
                                })
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->helperText('Super Admin: full control. Admin: manage records. Loans Officer: view only.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->whereHas('roles', fn ($q) =>
                $q->whereIn('name', ['super_admin', 'admin', 'loans_officer'])
            ))
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->default('N/A'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'super_admin'   => 'danger',
                        'admin'         => 'warning',
                        'loans_officer' => 'info',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin'   => 'Super Admin',
                        'admin'         => 'Admin',
                        'loans_officer' => 'Loans Officer',
                        default         => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->id !== Auth::id()),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'view'   => Pages\ViewAdminUser::route('/{record}'),
            'edit'   => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}