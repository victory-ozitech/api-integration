<?php

namespace App\Services;

use App\Models\PostChannel;
use App\Models\SchedulePost;
use Illuminate\Support\Facades\Http;

class FacebookPublisher
{
    private string $baseUrl = 'https://graph.facebook.com/v25.0/';

    public function publish(SchedulePost $post, array $uploadedFiles): void
    {
        foreach ($post->channels as $postChannel) {
            $channel = $postChannel->channel;

            if (!$channel) continue;

            try {
                $response = $this->sendToFacebook($channel, $post->message, $uploadedFiles);
                $data = $response->json();

                if (isset($data['error'])) {
                    $postChannel->update([
                        'status' => 'failed',
                        'error_message' => $data['error']['message'] ?? 'Unknown error',
                    ]);
                    logger()->error('Facebook Error', $data);
                    continue;
                }

                $postChannel->update([
                    'facebook_post_id' => $data['id'] ?? null,
                    'status' => 'published',
                ]);

                logger()->info('Facebook Response', $data);

            } catch (\Exception $e) {
                $postChannel->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                logger()->error('Facebook Exception', ['error' => $e->getMessage()]);
            }
        }
    }

    private function post(string $endpoint, array $data)
    {
        return Http::post($this->baseUrl . $endpoint, $data);
    }

    private function delete(string $endpoint, array $data)
    {
        return Http::delete($this->baseUrl . $endpoint, $data);
    }

    /**
     * Stabilized: Expects a RAW system path now to avoid public_path nesting anomalies.
     */
    private function attachFile(
        string $endpoint,
        string $absoluteFilePath,
        string $fileName,
        array $data,
        int $timeout = 30
    ) {
        return Http::timeout($timeout)
            ->attach(
                'source',
                fopen($absoluteFilePath, 'r'),
                $fileName
            )
            ->post(
                $this->baseUrl . $endpoint,
                $data
            );
    }
    
    private function sendToFacebook($channel, string $message, array $uploadedFiles)
    {
        $images = array_filter($uploadedFiles, fn($f) => str_starts_with($f['mime_type'], 'image/'));
        $videos = array_filter($uploadedFiles, fn($f) => str_starts_with($f['mime_type'], 'video/'));

        if (count($videos) === 1 && count($images) === 0) {
            return $this->postVideo($channel, $message, array_values($videos)[0]);
        }

        if (count($images) > 1) {
            return $this->postMultipleImages($channel, $message, $images);
        }

        if (count($images) === 1) {
            return $this->postSingleImage($channel, $message, array_values($images)[0]);
        }

        return $this->postText($channel, $message);
    }

    /*
    |------------------------------------------
    | MULTIPLE IMAGES
    |------------------------------------------
    | Fixed: Uses raw standard form post to complete the compilation feed
    */
    private function postMultipleImages($channel, string $message, array $images)
    {
        $mediaIds = [];

        foreach ($images as $image) {
            $absolutePath = public_path($image['file_path']);

            if (!file_exists($absolutePath)) {
                logger()->error('File not found: ' . $absolutePath);
                continue;
            }

            $uploadResponse = $this->attachFile(
                "{$channel->channel_id}/photos",
                $absolutePath,
                $image['file_name'],
                [
                    'published' => 'false',
                    'access_token' => $channel->access_token,
                ]
            );

            $uploadData = $uploadResponse->json();

            if (isset($uploadData['id'])) {
                $mediaIds[] = $uploadData['id'];
            } else {
                logger()->error('Image upload failed', $uploadData);
            }
        }

        // All uploads failed, fall back to text only
        if (empty($mediaIds)) {
            return $this->postText($channel, $message);
        }

        $attachedMedia = array_map(fn($id) => ['media_fbid' => $id], $mediaIds);

        // Correct Implementation: Send the compiled album references via JSON
        return $this->post("{$channel->channel_id}/feed", [
            'message' => $message,
            'access_token' => $channel->access_token,
            'attached_media' => $attachedMedia, // Laravel HTTP client auto-encodes arrays
        ]);
    }

    /*
    |------------------------------------------
    | SINGLE IMAGE
    |------------------------------------------
    */
    private function postSingleImage($channel, string $message, array $image)
    {
        return $this->attachFile(
            "{$channel->channel_id}/photos",
            public_path($image['file_path']),
            $image['file_name'],
            [
                'message' => $message,
                'access_token' => $channel->access_token,
            ]
        );
    }

    /*
    |------------------------------------------
    | SINGLE VIDEO
    |------------------------------------------
    | Swept out duplicate artifacts here
    */
    private function postVideo($channel, string $message, array $video)
    {
        return $this->attachFile(
            "{$channel->channel_id}/videos",
            public_path($video['file_path']),
            $video['file_name'],
            [
                'description' => $message,
                'access_token' => $channel->access_token,
            ],
            120
        );
    }

    /*
    |------------------------------------------
    | TEXT ONLY
    |------------------------------------------
    */
    private function postText($channel, string $message)
    {
        return $this->post("{$channel->channel_id}/feed", [
            'message' => $message,
            'access_token' => $channel->access_token,
        ]);
    }

    public function updatePost(string $facebookPostId, string $message, $channel): void
    {
        $this->post("{$facebookPostId}", [
            'message' => $message,
            'access_token' => $channel->access_token,
        ]);
    }

    public function deletePost(string $facebookPostId, $channel): void
    {
        $this->delete("{$facebookPostId}", [
            'access_token' => $channel->access_token,
        ]);
    }
}