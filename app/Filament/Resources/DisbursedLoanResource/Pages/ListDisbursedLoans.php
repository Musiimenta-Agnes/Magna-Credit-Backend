<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
class ListDisbursedLoans extends ListRecords
{
    protected static string $resource = DisbursedLoanResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
