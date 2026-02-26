<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'other_contact',
        'address',
        'bio',
        'gender',
        'occupation',
        'education',
        'profile_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}