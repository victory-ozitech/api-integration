<?php

namespace App\Console\Commands;

use App\Models\SchedulePost;
use App\Services\FacebookPublisher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PublishScheduledPosts extends Command
{
    protected $signature = 'app:publish-scheduled-posts';
    protected $description = 'Publish scheduled posts that are due';

    public function handle(FacebookPublisher $publisher)
    {
        $posts = SchedulePost::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->with(['channels.channel', 'media'])
            ->get();

        $this->info("Found {$posts->count()} scheduled posts.");

        foreach ($posts as $post) {
            $uploadedFiles = $post->media->map(fn($m) => [
                'file_path' => $m->file_path,
                'file_name' => $m->file_name,
                'mime_type' => $m->mime_type,
            ])->toArray();

            $publisher->publish($post, $uploadedFiles);

            $post->update([
                'status'       => 'published',
                'published_at' => now(),
            ]);

            $this->info("Published post #{$post->id}");
        }

        return self::SUCCESS;
    }
}