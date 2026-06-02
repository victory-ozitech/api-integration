<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_account_id',
        'platform',
        'channel_id',
        'channel_name',
        'channel_description',
        'access_token',
        'avatar'
    ];

    public function facebookAccount()
    {
        return $this->belongsTo(FacebookAccount::class);
    }

    public function postChannels()
    {
        return $this->hasMany(PostChannel::class);
    }
}