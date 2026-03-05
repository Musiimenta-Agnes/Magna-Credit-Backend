<?php
namespace App\Filament\Resources\RejectedLoanResource\Pages;
use App\Filament\Resources\RejectedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditRejectedLoan extends EditRecord
{
    protected static string $resource = RejectedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
