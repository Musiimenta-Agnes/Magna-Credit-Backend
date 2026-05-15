<?php
namespace App\Filament\Resources\RejectedLoanResource\Pages;
use App\Filament\Resources\RejectedLoanResource;
use Filament\Resources\Pages\CreateRecord;
class CreateRejectedLoan extends CreateRecord
{
    protected static string $resource = RejectedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'rejected';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
