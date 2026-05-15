<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\CreateRecord;
class CreateDisbursedLoan extends CreateRecord
{
    protected static string $resource = DisbursedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'disbursed';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
