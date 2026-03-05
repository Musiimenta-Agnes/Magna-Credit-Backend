<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
class ListPendingLoans extends ListRecords
{
    protected static string $resource = PendingLoanResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
