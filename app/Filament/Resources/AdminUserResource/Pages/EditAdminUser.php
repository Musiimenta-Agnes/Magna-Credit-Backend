<?php
namespace App\Filament\Resources\AdminUserResource\Pages;
use App\Filament\Resources\AdminUserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
class EditAdminUser extends EditRecord
{
    protected static string $resource = AdminUserResource::class;
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->id !== Auth::id())
                ->before(function () {
                    Notification::make()
                        ->danger()
                        ->title('Admin user deleted successfully')
                        ->send();
                }),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Admin user updated successfully');
    }
}
