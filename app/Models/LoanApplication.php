<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'contact', 'email', 'bio_info', 'location',
        'other_contact', 'gender', 'kin_name', 'kin_contact', 'occupation',
        'monthly_income', 'loan_amount', 'loan_type', 'education', 'address',
        'national_id_image', 'collateral_images', 'status',
        'disbursement_date', 'due_date', 'rejection_reason',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'collateral_images' => 'array',
        'monthly_income'    => 'decimal:2',
        'loan_amount'       => 'decimal:2',
        'disbursement_date' => 'date',
        'due_date'          => 'date',
        'reviewed_at'       => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function getTotalRepaidAttribute()
    {
        return $this->repayments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->loan_amount - $this->total_repaid;
    }
}
