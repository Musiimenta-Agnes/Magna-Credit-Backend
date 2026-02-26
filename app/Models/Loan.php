<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'loan_type',
        'monthly_income',
        'next_of_kin_name',
        'next_of_kin_contact',
        'current_address',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}