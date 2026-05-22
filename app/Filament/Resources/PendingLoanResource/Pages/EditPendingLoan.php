<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class EditPendingLoan extends EditRecord
{
    protected static string $resource = PendingLoanResource::class;

    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin']))
                ->successNotificationTitle('Pending loan deleted successfully'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pending loan updated successfully');
    }
}