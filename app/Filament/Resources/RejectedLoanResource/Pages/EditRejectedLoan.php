<?php
namespace App\Filament\Resources\RejectedLoanResource\Pages;
use App\Filament\Resources\RejectedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditRejectedLoan extends EditRecord
{
    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount($record);
    }
    protected static string $resource = RejectedLoanResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
