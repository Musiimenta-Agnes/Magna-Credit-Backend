<?php
namespace App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Admin user created successfully');
    }
}
