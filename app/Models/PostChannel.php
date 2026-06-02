<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChannel extends Model
{
    protected $fillable = [
        'schedule_post_id',
        'channel_id',
        'platform',
        'facebook_post_id',
        'status',
    ];

    public function schedulePost()
    {
        return $this->belongsTo(SchedulePost::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
