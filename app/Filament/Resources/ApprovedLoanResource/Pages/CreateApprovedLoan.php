<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\CreateRecord;
class CreateApprovedLoan extends CreateRecord
{
    protected static string $resource = ApprovedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'approved';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
