<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditDisbursedLoan extends EditRecord
{
    protected static string $resource = DisbursedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
