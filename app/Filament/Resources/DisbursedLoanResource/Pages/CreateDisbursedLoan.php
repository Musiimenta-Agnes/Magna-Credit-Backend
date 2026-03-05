<?php
namespace App\Filament\Resources\DisbursedLoanResource\Pages;
use App\Filament\Resources\DisbursedLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDisbursedLoan extends CreateRecord
{
    protected static string $resource = DisbursedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'disbursed';
        return $data;
    }
}
