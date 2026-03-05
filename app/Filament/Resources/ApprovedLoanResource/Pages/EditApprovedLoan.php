<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditApprovedLoan extends EditRecord
{
    protected static string $resource = ApprovedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
