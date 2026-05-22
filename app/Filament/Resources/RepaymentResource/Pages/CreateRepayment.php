<?php
namespace App\Filament\Resources\RepaymentResource\Pages;
use App\Filament\Resources\RepaymentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRepayment extends CreateRecord
{
    protected static string $resource = RepaymentResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Repayment recorded successfully');
    }
}