<?php

namespace App\Observers;

use App\Models\Repayment;
use App\Models\UserNotification;

class RepaymentObserver
{
    public function created(Repayment $repayment): void
    {
        $loan = $repayment->loanApplication;

        UserNotification::create([
            'user_id'      => $repayment->user_id,
            'title'        => 'Repayment recorded.',
            'body'         => 'A repayment of UGX ' . number_format($repayment->amount) . ' has been recorded on your ' . ($loan->loan_type ?? 'loan') . '. Outstanding balance: UGX ' . number_format($loan->balance ?? 0) . '.',
            'type'         => 'repayment',
            'reference_id' => $repayment->loan_application_id,
            'is_read'      => false,
        ]);
    }
}
