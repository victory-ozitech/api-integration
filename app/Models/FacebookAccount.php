<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_id',
        'name',
        'email',
        'avatar',
        'access_token',
        'expires_in',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
