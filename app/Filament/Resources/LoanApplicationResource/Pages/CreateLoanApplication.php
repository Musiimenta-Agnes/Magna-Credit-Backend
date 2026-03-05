<?php
namespace App\Filament\Resources\LoanApplicationResource\Pages;
use App\Filament\Resources\LoanApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanApplication extends CreateRecord
{
    protected static string $resource = LoanApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
