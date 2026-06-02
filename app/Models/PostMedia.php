<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    protected $fillable = [
        'schedule_post_id',
        'file_path',
        'file_name',
        'mime_type',
        'media_type',
        'file_size'
    ];

    public function schedulePost()
    {
        return $this->belongsTo(SchedulePost::class);
    }
}
