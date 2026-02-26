<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplicationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'kin_name',
        'kin_contact',
        'occupation',
        'monthly_income',
        'loan_amount',        // ← NEW
        'loan_type',
        'education',
        'address',
        'national_id_image',
        'collateral_images',
    ];

    protected $casts = [
        'collateral_images' => 'array',
    ];
}











// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class LoanApplicationDetail extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'loan_application_id',
//         'kin_name',
//         'kin_contact',
//         'occupation',
//         'monthly_income',
//         'loan_type',
//         'education',
//         'address',
//         'national_id_image',
//         'collateral_images',
//     ];

//     protected $casts = [
//         'collateral_images' => 'array',
//     ];
// } 
