<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $slug = 'activity-logs';
    protected static ?string $modelLabel = 'Activity Log';
    protected static ?string $pluralModelLabel = 'Activity Logs';

    public static function getNavigationLabel(): string
    {
        return 'Activity Logs';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 100;
    }

    // Only super_admin can see or access this resource
    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Admin/User')
                    ->default('System/Guest')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Logged In'                 => 'success',
                        'Logged Out'                => 'gray',
                        'Created Loan Application'  => 'info',
                        'Updated Loan Application'  => 'warning',
                        'Deleted Loan Application'  => 'danger',
                        'Recorded Repayment'        => 'success',
                        'Created Admin/Client'      => 'info',
                        'Updated Admin/Client'      => 'warning',
                        'Deleted Admin/Client'      => 'danger',
                        default                     => 'primary',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Logged On')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
