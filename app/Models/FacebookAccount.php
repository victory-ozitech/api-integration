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

        'page_id',
        'page_name',
        'page_access_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
