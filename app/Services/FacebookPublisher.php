<?php

namespace App\Services;

use App\Models\PostChannel;
use App\Models\SchedulePost;
use Illuminate\Support\Facades\Http;

class FacebookPublisher
{
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
    */
    private function postMultipleImages($channel, string $message, array $images)
    {
        $mediaIds = [];

        foreach ($images as $image) {
            $filePath = public_path($image['file_path']);

            if (!file_exists($filePath)) {
                logger()->error('File not found: ' . $filePath);
                continue;
            }

            $uploadResponse = Http::attach(
                'source',
                fopen($filePath, 'r'),
                $image['file_name']
            )->post(
                "https://graph.facebook.com/v25.0/{$channel->channel_id}/photos",
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

        return Http::asForm()->post(
            "https://graph.facebook.com/v25.0/{$channel->channel_id}/feed",
            [
                'message' => $message,
                'access_token' => $channel->access_token,
                'attached_media' => json_encode($attachedMedia),
            ]
        );
    }

    /*
    |------------------------------------------
    | SINGLE IMAGE
    |------------------------------------------
    */
    private function postSingleImage($channel, string $message, array $image)
    {
        return Http::attach(
            'source',
            fopen(public_path($image['file_path']), 'r'),
            $image['file_name']
        )->post(
            "https://graph.facebook.com/v25.0/{$channel->channel_id}/photos",
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
    */
    private function postVideo($channel, string $message, array $video)
    {
        return Http::timeout(120)->attach(
            'source',
            fopen(public_path($video['file_path']), 'r'),
            $video['file_name']
        )->post(
            "https://graph.facebook.com/v25.0/{$channel->channel_id}/videos",
            [
                'description' => $message,
                'access_token' => $channel->access_token,
            ]
        );
    }

    /*
    |------------------------------------------
    | TEXT ONLY
    |------------------------------------------
    */
    private function postText($channel, string $message)
    {
        return Http::post(
            "https://graph.facebook.com/v25.0/{$channel->channel_id}/feed",
            [
                'message' => $message,
                'access_token' => $channel->access_token,
            ]
        );
    }

    public function updatePost(string $facebookPostId, string $message, $channel): void
    {
        // Facebook only allows editing the message, not media
        Http::post("https://graph.facebook.com/v25.0/{$facebookPostId}", [
            'message' => $message,
            'access_token' => $channel->access_token,
        ]);
    }

    public function deletePost(string $facebookPostId, $channel): void
    {
        Http::delete("https://graph.facebook.com/v25.0/{$facebookPostId}", [
            'access_token' => $channel->access_token,
        ]);
    }

}