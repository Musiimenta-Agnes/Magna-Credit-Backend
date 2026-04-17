<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDisbursedLoan extends CreateRecord
{
    public function mount(): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount();
    }
    protected static string $resource = DisbursedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'disbursed';
        return $data;
    }
}
