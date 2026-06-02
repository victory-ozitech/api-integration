<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str; 

class FacebookPostService
{
    public function publish(
        string $pageId,
        string $accessToken,
        string $message
    )
    {
        return Http::post(
            "https://graph.facebook.com/v25.0/{$pageId}/feed",
            [
                'message' => $message,
                'access_token' => $accessToken,
            ]
        )->json();
    }
}
