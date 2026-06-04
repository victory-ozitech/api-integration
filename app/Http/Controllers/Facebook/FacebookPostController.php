<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\PostChannel;
use App\Models\PostMedia;
use App\Models\SchedulePost;
use App\Services\FacebookPublisher;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FacebookPostController extends Controller
{
    public function __construct(
        protected FacebookPublisher $facebookPublisher
    ) {}

    public function index()
    {
        $posts = SchedulePost::where('user_id', 1)
            ->with(['channels.channel', 'media'])
            ->latest()
            ->get();

        return Inertia::render('User/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function store(Request $request)
    {
        // return $request->all();
        $request->validate([
            'message'      => 'nullable|string',
            'channels'     => 'required|array|min:1',
            'scheduled_at' => 'nullable|date',
            'media'        => 'nullable|array',
            'media.*'      => 'file|max:51200',
        ]);

        $isScheduled = $this->isScheduled($request->scheduled_at);

        $post = SchedulePost::create([
            'user_id'      => 1,
            'message'      => $request->message,
            'scheduled_at' => $request->scheduled_at,
            'status'       => $isScheduled ? 'scheduled' : 'published',
            'published_at' => $isScheduled ? null : now(),
            'is_scheduled' => $isScheduled ? 1 : 0,
        ]);

        $uploadedFiles = $this->saveMedia($request, $post);
        $this->saveChannels($request->channels, $post, $isScheduled);

        if (!$isScheduled) {
            $this->facebookPublisher->publish($post, $uploadedFiles);
        }

        return redirect()->route('posts.index')->with(
            'success',
            $isScheduled ? 'Post scheduled successfully.' : 'Post published successfully.'
        );
    }

    public function update(Request $request, SchedulePost $post)
    {
        
        dd($request->all());
        $request->validate([
            'message'      => 'nullable|string',
            'scheduled_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($request->hasFile('media')) {
            $request->validate([
                'media.*' => 'file|max:51200',
            ]);
        }

        $isScheduled = $request->is_scheduled === '1' 
        && !empty($request->scheduled_at) 
        && now()->lt($request->scheduled_at);
        $wasPublished = $post->status === 'published';

        $post->update([
            'message'      => $request->message,
            'scheduled_at' => $request->scheduled_at,
            'status'       => $isScheduled ? 'scheduled' : 'published',
            'published_at' => $isScheduled ? null : now(),
            'is_scheduled' => $isScheduled ? 1 : 0,
        ]);

        [$uploadedFiles, $mediaChanged] = $this->updateMedia($request, $post);

        if (!$isScheduled) {
            $this->handleFacebookUpdate($post, $request->message, $uploadedFiles, $wasPublished, $mediaChanged);
        }

        return redirect()->route('posts.index')->with(
            'success',
            $isScheduled ? 'Post updated successfully.' : 'Post published successfully.'
        );
    }

    public function destroy(SchedulePost $post)
    {
        if ($post->status === 'published') {
            foreach ($post->channels as $postChannel) {
                if ($postChannel->facebook_post_id) {
                    $this->facebookPublisher->deletePost(
                        $postChannel->facebook_post_id,
                        $postChannel->channel
                    );
                }
            }
        }

        foreach ($post->media as $media) {
            $filePath = public_path($media->file_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    private function isScheduled(?string $scheduledAt): bool
    {
        return !empty($scheduledAt) && now()->lt($scheduledAt);
    }

    private function saveMedia(Request $request, SchedulePost $post): array
    {
        if (!$request->hasFile('media')) return [];

        $uploadedFiles = UploadService::upload($request->file('media'), 'posts');

        foreach ($uploadedFiles as $file) {
            PostMedia::create([
                'schedule_post_id' => $post->id,
                'file_path'        => $file['file_path'],
                'file_name'        => $file['file_name'],
                'mime_type'        => $file['mime_type'],
                'file_size'        => $file['file_size'],
            ]);
        }

        return $uploadedFiles;
    }

    private function updateMedia(Request $request, SchedulePost $post): array
    {
        $hasNewFiles = $request->hasFile('media');
        $keptMediaIds = $request->input('existing_media', []);

        // Delete only media the user removed
        foreach ($post->media as $oldMedia) {
            if (!in_array($oldMedia->id, $keptMediaIds)) {
                $oldPath = public_path($oldMedia->file_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $oldMedia->delete();
            }
        }

        // Upload new files if any
        $newlyUploadedFiles = [];

        if ($hasNewFiles) {
            $newlyUploadedFiles = UploadService::upload(
                $request->file('media'),
                'posts'
            );

            foreach ($newlyUploadedFiles as $file) {
                PostMedia::create([
                    'schedule_post_id' => $post->id,
                    'file_path'        => $file['file_path'],
                    'file_name'        => $file['file_name'],
                    'mime_type'        => $file['mime_type'],
                    'file_size'        => $file['file_size'],
                ]);
            }
        }

        // Build full uploadedFiles array for FacebookPublisher
        // Combine kept existing media + newly uploaded files
        $post->refresh();

        $allFiles = $post->media->map(fn($m) => [
            'file_path' => $m->file_path,
            'file_name' => $m->file_name,
            'mime_type' => $m->mime_type,
        ])->toArray();

        $mediaChanged = count($keptMediaIds) !== $post->media->count() || $hasNewFiles;

        return [$allFiles, $mediaChanged];
    }

    private function saveChannels(array $channels, SchedulePost $post, bool $isScheduled): void
    {
        foreach ($channels as $selectedChannel) {
            $channel = Channel::find($selectedChannel['channel_id']);

            if (!$channel) continue;

            PostChannel::create([
                'schedule_post_id' => $post->id,
                'channel_id'       => $channel->id,
                'status'           => $isScheduled ? 'pending' : 'published',
            ]);
        }
    }

    private function handleFacebookUpdate(
        SchedulePost $post,
        string $message,
        array $uploadedFiles,
        bool $wasPublished,
        bool $mediaChanged
    ): void {
        if ($wasPublished && $mediaChanged) {
            // Delete old Facebook post and republish with new media
            foreach ($post->channels as $postChannel) {
                if ($postChannel->facebook_post_id) {
                    $this->facebookPublisher->deletePost(
                        $postChannel->facebook_post_id,
                        $postChannel->channel
                    );
                }
            }
            $this->facebookPublisher->publish($post, $uploadedFiles);

        } elseif ($wasPublished && !$mediaChanged) {
            // Only message changed
            foreach ($post->channels as $postChannel) {
                if ($postChannel->facebook_post_id) {
                    $this->facebookPublisher->updatePost(
                        $postChannel->facebook_post_id,
                        $message,
                        $postChannel->channel
                    );
                }
            }
        } else {
            // Was scheduled, publishing for first time
            $this->facebookPublisher->publish($post, $uploadedFiles);
        }
    }
}