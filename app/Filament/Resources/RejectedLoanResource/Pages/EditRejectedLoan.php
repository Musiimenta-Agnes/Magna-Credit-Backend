<?php
namespace App\Filament\Resources\RejectedLoanResource\Pages;
use App\Filament\Resources\RejectedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditRejectedLoan extends EditRecord
{
    protected static string $resource = RejectedLoanResource::class;

    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $this->redirect($this->getResource()::getUrl('index'));
            return;
        }
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
        ];
    }
}
