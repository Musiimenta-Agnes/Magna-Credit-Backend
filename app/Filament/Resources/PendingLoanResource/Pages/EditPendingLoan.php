<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditPendingLoan extends EditRecord
{
    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount($record);
    }
    protected static string $resource = PendingLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
