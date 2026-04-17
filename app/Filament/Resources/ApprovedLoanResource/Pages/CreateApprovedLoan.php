<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApprovedLoan extends CreateRecord
{
    public function mount(): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
        parent::mount();
    }
    protected static string $resource = ApprovedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'approved';
        return $data;
    }
}
