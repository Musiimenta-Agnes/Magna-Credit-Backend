<?php

namespace App\Observers;

use App\Models\LoanApplication;
use App\Models\UserNotification;

class LoanApplicationObserver
{
    public function updated(LoanApplication $loan): void
    {
        if (!$loan->isDirty('status')) return;

        $status = $loan->status;

        $map = [
            'approved'  => ['Your loan has been approved!',        'Great news! Your ' . $loan->loan_type . ' of UGX ' . number_format($loan->loan_amount) . ' has been approved.'],
            'rejected'  => ['Your loan application was rejected.',  'Unfortunately your ' . $loan->loan_type . ' application was not approved. Reason: ' . ($loan->rejection_reason ?? 'Not specified') . '.'],
            'disbursed' => ['Your loan has been disbursed!',        'UGX ' . number_format($loan->loan_amount) . ' for your ' . $loan->loan_type . ' has been sent to you.'],
            'repaying'  => ['Repayment started.',                   'Your ' . $loan->loan_type . ' is now in repayment. Keep up with your payments.'],
            'completed' => ['Loan fully repaid!',                   'Congratulations! You have fully repaid your ' . $loan->loan_type . '. Thank you!'],
        ];

        if (!isset($map[$status])) return;

        $title = $map[$status][0];
        $body  = $map[$status][1];

        UserNotification::create([
            'user_id'      => $loan->user_id,
            'title'        => $title,
            'body'         => $body,
            'type'         => 'loan_status',
            'reference_id' => $loan->id,
            'is_read'      => false,
        ]);
    }
}
