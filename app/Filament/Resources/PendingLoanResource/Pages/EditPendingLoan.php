<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditPendingLoan extends EditRecord {
    protected static string $resource = PendingLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
