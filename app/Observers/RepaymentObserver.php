<?php

namespace App\Observers;

use App\Models\Repayment;
use App\Models\UserNotification;

class RepaymentObserver
{
    public function created(Repayment $repayment): void
    {
        $loan = $repayment->loanApplication;
        $actor = \Illuminate\Support\Facades\Auth::user();
        $action = $actor ? 'Recorded Repayment' : 'Submitted Repayment';
        
        \App\Models\ActivityLog::create([
            'user_id' => $actor?->id ?? $repayment->user_id,
            'action' => $action,
            'description' => "Repayment of UGX " . number_format($repayment->amount) . " for loan application #{$repayment->loan_application_id} (Client: '{$loan->name}') was recorded.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        UserNotification::create([
            'user_id'      => $repayment->user_id,
            'title'        => 'Repayment recorded.',
            'body'         => 'A repayment of UGX ' . number_format($repayment->amount) . ' has been recorded on your ' . ($loan->loan_type ?? 'loan') . '. Outstanding balance: UGX ' . number_format($loan->balance ?? 0) . '.',
            'type'         => 'repayment',
            'reference_id' => $repayment->loan_application_id,
            'is_read'      => false,
        ]);
    }

    public function deleted(Repayment $repayment): void
    {
        $actor = \Illuminate\Support\Facades\Auth::user();
        if ($actor) {
            \App\Models\ActivityLog::create([
                'user_id' => $actor->id,
                'action' => 'Deleted Repayment',
                'description' => "Repayment #{$repayment->id} of UGX " . number_format($repayment->amount) . " was deleted.",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
