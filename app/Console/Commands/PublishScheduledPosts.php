<?php

namespace App\Console\Commands;

use App\Jobs\PublishFacebookPostJob;
use App\Models\SchedulePost;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = SchedulePost::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($posts as $post) {

            dump("Publishing Post {$post->id}");

            $post->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

        }
    }
}
