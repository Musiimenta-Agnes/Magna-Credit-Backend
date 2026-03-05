<?php
namespace App\Filament\Resources\ApprovedLoanResource\Pages;
use App\Filament\Resources\ApprovedLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApprovedLoan extends CreateRecord
{
    protected static string $resource = ApprovedLoanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = 'approved';
        return $data;
    }
}
