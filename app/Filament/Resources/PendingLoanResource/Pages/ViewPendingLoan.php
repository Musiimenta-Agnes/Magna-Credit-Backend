<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewPendingLoan extends ViewRecord
{
    protected static string $resource = PendingLoanResource::class;
}