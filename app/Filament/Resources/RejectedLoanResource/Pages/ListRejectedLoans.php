<?php
namespace App\Filament\Resources\RejectedLoanResource\Pages;
use App\Filament\Resources\RejectedLoanResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
class ListRejectedLoans extends ListRecords
{
    protected static string $resource = RejectedLoanResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
