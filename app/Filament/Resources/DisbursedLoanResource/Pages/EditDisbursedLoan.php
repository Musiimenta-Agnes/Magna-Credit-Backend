<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditDisbursedLoan extends EditRecord
{
    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount($record);
    }
    protected static string $resource = DisbursedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
