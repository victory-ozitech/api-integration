<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishFacebookPostJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $facebookAccount = $this->post->facebookAccount;

        $response = Http::post(
            "https://graph.facebook.com/v25.0/{$facebookAccount->page_id}/feed",
            [
                'message' => $this->post->message,
                'access_token' => $facebookAccount->page_access_token,
            ]
        );

        $data = $response->json();

        $this->post->update([

            'status' => 'published',

            'published_at' => now(),

            'facebook_post_id' => $data['id'] ?? null,
        ]);
    }
}
