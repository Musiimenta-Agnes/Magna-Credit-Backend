<?php
namespace App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->successNotificationTitle('Admin user created successfully'),
        ];
    }
}
