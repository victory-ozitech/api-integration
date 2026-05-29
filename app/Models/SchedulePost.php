<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulePost extends Model
{
    protected $fillable = [
        'user_id',
        'facebook_account_id',
        'message',
        'scheduled_at',
        'published_at',
        'status',
        'facebook_post_id',
    ];

    public function facebookAccount()
    {
        return $this->belongsTo(FacebookAccount::class);
    }
}
