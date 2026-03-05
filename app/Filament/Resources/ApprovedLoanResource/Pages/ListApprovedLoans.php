<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
class ListApprovedLoans extends ListRecords
{
    protected static string $resource = ApprovedLoanResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
