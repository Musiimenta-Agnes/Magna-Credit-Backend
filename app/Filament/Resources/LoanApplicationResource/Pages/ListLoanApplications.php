<?php
namespace App\Filament\Resources\LoanApplicationResource\Pages;
use App\Filament\Resources\LoanApplicationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListLoanApplications extends ListRecords
{
    protected static string $resource = LoanApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return auth()->user()?->hasRole('super_admin') ? [
            CreateAction::make()->label('New Application'),
        ] : [];
    }
}
