<?php
namespace App\Filament\Resources\RepaymentResource\Pages;
use App\Filament\Resources\RepaymentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;

class ListRepayments extends ListRecords
{
    protected static string $resource = RepaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->successNotificationTitle('Repayment recorded successfully'),
        ];
    }
}