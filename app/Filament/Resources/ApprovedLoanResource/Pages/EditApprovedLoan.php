<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditApprovedLoan extends EditRecord
{
    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount($record);
    }
    protected static string $resource = ApprovedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
