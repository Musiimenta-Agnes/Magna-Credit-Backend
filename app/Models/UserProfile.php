<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'bio',
        'address',
        'other_contact',
        'kin_name',
        'kin_contact',
        'income',
        'current_address',
        'gender',
        'occupation',
        'loan_type',
        'education',
        'profile_image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}