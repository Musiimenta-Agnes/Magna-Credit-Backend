<?php

namespace App\Observers;

use App\Models\LoanApplication;
use App\Models\UserNotification;

class LoanApplicationObserver
{
    public function created(LoanApplication $loan): void
    {
        $actor = \Illuminate\Support\Facades\Auth::user();
        $action = $actor ? 'Created Loan Application' : 'Submitted Loan Application';
        
        \App\Models\ActivityLog::create([
            'user_id' => $actor?->id ?? $loan->user_id,
            'action' => $action,
            'description' => "Loan application #{$loan->id} of UGX " . number_format($loan->loan_amount) . " for client '{$loan->name}' was created.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function updated(LoanApplication $loan): void
    {
        // Log the updates if performed by an admin
        $actor = \Illuminate\Support\Facades\Auth::user();
        if ($actor) {
            $dirty = $loan->getDirty();
            unset($dirty['updated_at']);
            
            if (!empty($dirty)) {
                $changes = [];
                foreach ($dirty as $field => $newValue) {
                    $oldValue = $loan->getOriginal($field);
                    if ($field === 'status') {
                        $changes[] = "status changed from '{$oldValue}' to '{$newValue}'";
                    } elseif (in_array($field, ['loan_amount', 'monthly_income'])) {
                        $changes[] = "$field changed from UGX " . number_format((float)$oldValue) . " to UGX " . number_format((float)$newValue);
                    } else {
                        $changes[] = "$field changed";
                    }
                }
                
                \App\Models\ActivityLog::create([
                    'user_id' => $actor->id,
                    'action' => 'Updated Loan Application',
                    'description' => "Loan application #{$loan->id} for '{$loan->name}' was updated: " . implode(', ', $changes),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }

        // Send notification to user if status changed
        if (!$loan->isDirty('status')) return;

        // Skip notification for walk-in customers with no linked user account
        if (!$loan->user_id) return;

        $status = $loan->status;

        $map = [
            'approved'  => ['Your loan has been approved!',        'Great news! Your ' . $loan->loan_type . ' of UGX ' . number_format($loan->loan_amount) . ' has been approved.'],
            'rejected'  => ['Your loan application was rejected.',  'Unfortunately your ' . $loan->loan_type . ' application was not approved. Reason: ' . ($loan->rejection_reason ?? 'Not specified') . '.'],
            'disbursed' => ['Your loan has been disbursed!',        'UGX ' . number_format($loan->loan_amount) . ' for your ' . $loan->loan_type . ' has been sent to you.'],
            'repaying'  => ['Repayment started.',                   'Your ' . $loan->loan_type . ' is now in repayment. Keep up with your payments.'],
            'completed' => ['Loan fully repaid!',                   'Congratulations! You have fully repaid your ' . $loan->loan_type . '. Thank you!'],
        ];

        if (!isset($map[$status])) return;

        [$title, $body] = $map[$status];

        UserNotification::create([
            'user_id'      => $loan->user_id,
            'title'        => $title,
            'body'         => $body,
            'type'         => 'loan_status',
            'reference_id' => $loan->id,
            'is_read'      => false,
        ]);
    }

    public function deleted(LoanApplication $loan): void
    {
        $actor = \Illuminate\Support\Facades\Auth::user();
        if ($actor) {
            \App\Models\ActivityLog::create([
                'user_id' => $actor->id,
                'action' => 'Deleted Loan Application',
                'description' => "Loan application #{$loan->id} for '{$loan->name}' was deleted.",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}