<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'reference_id',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
