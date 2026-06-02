<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulePost extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'scheduled_at',
        'published_at',
        'status',
    ];
    
    public function channels()
    {
        return $this->hasMany(PostChannel::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class);
    }
}
