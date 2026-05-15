<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;
class EditApprovedLoan extends EditRecord
{
    protected static string $resource = ApprovedLoanResource::class;

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
