<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        // Page 1
        'user_id',
        'name',
        'contact',
        'email',
        'bio_info',
        'location',
        'other_contact',
        'gender',

        // Page 2
        'kin_name',
        'kin_contact',
        'occupation',
        'monthly_income',
        'loan_amount',
        'loan_type',
        'education',
        'address',

        // Documents
        'national_id_image',
        'collateral_images',

        // Status
        'status',
    ];

    protected $casts = [
        'collateral_images' => 'array',
        'monthly_income'    => 'decimal:2',
        'loan_amount'       => 'decimal:2',
    ];

    // Relationship back to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}