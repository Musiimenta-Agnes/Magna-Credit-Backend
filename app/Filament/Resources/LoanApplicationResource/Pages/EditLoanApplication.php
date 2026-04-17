<?php
namespace App\Filament\Resources\LoanApplicationResource\Pages;
use App\Filament\Resources\LoanApplicationResource;
use Filament\Resources\Pages\EditRecord;
class EditLoanApplication extends EditRecord {
    public function mount(int|string $record): void
    {
        if (!auth()->user()?->hasRole('super_admin')) {
            redirect()->route('filament.admin.resources.loan-applications.index');
        }
        parent::mount($record);
    }
    protected static string $resource = LoanApplicationResource::class;
}
