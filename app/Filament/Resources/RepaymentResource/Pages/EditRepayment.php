<?php
namespace App\Filament\Resources\RepaymentResource\Pages;
use App\Filament\Resources\RepaymentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRepayment extends EditRecord
{
    protected static string $resource = RepaymentResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Repayment updated successfully');
    }
}