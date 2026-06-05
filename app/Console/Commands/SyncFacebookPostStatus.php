<?php

namespace App\Console\Commands;

use App\Models\PostChannel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncFacebookPostStatus extends Command
{
    protected $signature = 'app:sync-facebook-status';
    protected $description = 'Check if published posts still exist on Facebook';

    public function handle()
    {
        $postChannels = PostChannel::where('status', 'published')
            ->whereNotNull('facebook_post_id')
            ->with('channel')
            ->get();

        $this->info("Checking {$postChannels->count()} published posts.");

        foreach ($postChannels as $postChannel) {
            if (!$postChannel->channel) continue;

            $response = Http::withoutVerifying()->get(
                "https://graph.facebook.com/v25.0/{$postChannel->facebook_post_id}",
                [
                    'access_token' => $postChannel->channel->access_token,
                    'fields'       => 'id',
                ]
            );

            $data = $response->json();

            if (isset($data['error'])) {
                $postChannel->update([
                    'status'           => 'deleted_on_platform',
                    'facebook_post_id' => null,
                ]);

                $this->info("Post {$postChannel->facebook_post_id} no longer exists on Facebook.");
            }
        }

        return self::SUCCESS;
    }
}