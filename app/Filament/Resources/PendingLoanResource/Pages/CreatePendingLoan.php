<?php
namespace App\Filament\Resources\PendingLoanResource\Pages;
use App\Filament\Resources\PendingLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePendingLoan extends CreateRecord
{
    public function mount(): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount();
    }
    protected static string $resource = PendingLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';
        return $data;
    }
}
