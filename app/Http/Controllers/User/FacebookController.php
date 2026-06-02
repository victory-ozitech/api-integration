<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\FacebookAccount;
use App\Models\PostChannel;
use App\Models\PostMedia;
use App\Models\SchedulePost;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class FacebookController extends Controller
{
       public function connect()
    {
        $facebookAccount = FacebookAccount::where('user_id', 1)->first();

            // Not connected yet — just show the connect button
        if (!$facebookAccount) {
            return Inertia::render('User/Facebook/ConnectPage', [
                'facebookAccount' => null,
                'pages' => [],
            ]);
        }

            // Connected — fetch their pages to display
        // $response = Http::get('https://graph.facebook.com/v25.0/me/accounts', [
        //     'access_token' => $facebookAccount->access_token,
        //     'fields' => 'id,name,access_token,category,picture',
        // ]);
        $response = Http::withoutVerifying()->get('https://graph.facebook.com/v25.0/me/accounts', [
            'access_token' => $facebookAccount->access_token,
            'fields' => 'id,name,access_token,category,picture',
        ]);

            $pagesData = $response->json();

            // return $pagesData;

            if (isset($pagesData['error'])) {
                // Token expired, make them reconnect
                $facebookAccount->delete();
                return redirect()->route('facebook.connect');
            }

            return Inertia::render('User/Facebook/ConnectPage', [
                'facebookAccount' => $facebookAccount,
                'pages' => $pagesData['data'] ?? [],
            ]);
    }

    public function selectedPage(Request $request)
    {
        $channel = Channel::findOrFail($request->channel_id);

        session([
            'selected_channel_id' => $channel->id
        ]);

        return back()->with(
            'success',
            'Page selected successfully.'
        );
    }

    public function redirectToFacebook()
    {

        $appId = env('FACEBOOK_APP_ID');
        $redirectUri = env('FACEBOOK_REDIRECT_URI');
        $state = csrf_token();
        session(['facebook_state' => $state]);
        $scope = implode(',', [
            'email',
            'public_profile',
            'pages_manage_posts',
            'pages_read_engagement',
            'pages_show_list',
            'pages_manage_engagement'
        ]);

        $query = http_build_query([
            'client_id' => $appId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => $scope,
            'response_type' => 'code',
            'auth_type' => 'rerequest',
        ]);
        $facebookAuthUrl = "https://www.facebook.com/v25.0/dialog/oauth?$query";
        return redirect($facebookAuthUrl);
    }

    public function handleFacebookCallback(Request $request)
    {
        if ($request->state !== session('facebook_state')) {
            abort(403, 'Invalid state');
        }

        // $response = Http::get('https://graph.facebook.com/v25.0/oauth/access_token', [
        //     'client_id' => env('FACEBOOK_APP_ID'),
        //     'client_secret' => env('FACEBOOK_APP_SECRET'),
        //     'redirect_uri' => env('FACEBOOK_REDIRECT_URI'),
        //     'code' => $request->code,
        // ]);

        $response = Http::withoutVerifying()->get('https://graph.facebook.com/v25.0/oauth/access_token', [
            'client_id' => env('FACEBOOK_APP_ID'),
            'client_secret' => env('FACEBOOK_APP_SECRET'),
            'redirect_uri' => env('FACEBOOK_REDIRECT_URI'),
            'code' => $request->code,
        ]);

        $tokenData = $response->json();

        if (!isset($tokenData['access_token'])) {
            return response()->json($tokenData, 400);
        }

        $accessToken = $tokenData['access_token'];

        // 2. Get user info
        // $userResponse = Http::get('https://graph.facebook.com/me', [
        //     'fields' => 'id,name,email,picture',
        //     'access_token' => $accessToken,
        // ]);

        $userResponse = Http::withoutVerifying()->get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email,picture',
            'access_token' => $accessToken,
        ]);


        $userData = $userResponse->json();

        $facebookAccount = FacebookAccount::updateOrCreate(
            [
                'facebook_id' => $userData['id']
            ],
            [
                'user_id' => 1,
                'name' => $userData['name'] ?? null,
                'email' => $userData['email'] ?? null,
                'avatar' => $userData['picture']['data']['url'] ?? null,
                'access_token' => $accessToken,
            ]
        );

        $pagesResponse = Http::withoutVerifying()->get(
            'https://graph.facebook.com/v25.0/me/accounts',
            [
                'access_token' => $accessToken,
                'fields' => 'id,name,picture,access_token',
            ]
        );
        $pages = $pagesResponse->json()['data'] ?? [];
        foreach ($pages as $page) {

            Channel::updateOrCreate(
                [
                    'user_id' => 1,
                    'channel_id' => $page['id'],
                ],
                [
                    'facebook_account_id' => $facebookAccount->id,
                    'platform' => 'facebook',
                    'channel_name' => $page['name'],
                    'access_token' => $page['access_token'],
                    'avatar' => $page['picture']['data']['url'] ?? null,
                ]
            );
        }

        // return Inertia::render('User/Dashboard', [
        //     'facebookAccount' => $facebookAccount
        // ]);
        return redirect()->route('facebook.connect')->with('success', 'Facebook connected successfully.');
    }

    public function schedulePost(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'channels' => 'required|array|min:1',
            'media.*' => 'nullable|file',
        ]);

        $post = SchedulePost::create([
            'user_id' => 1,
            'message' => $request->message,
            'scheduled_at' => $request->scheduled_at,
            'status' => $request->scheduled_at ? 'scheduled' : 'published',
        ]);

        foreach ($request->channels as $selectedChannel) {
            $channel = Channel::find($selectedChannel['channel_id']);
            if(!$channel) {
                continue; // Skip if channel not found
            }
            PostChannel::create([
                'schedule_post_id' => $post->id,
                'channel_id' => $selectedChannel['channel_id'],
                'platform' => 'facebook',
                'status' => $request->scheduled_at ? 'pending' : 'published',
            ]);
        }

        if ($request->hasFile('media')) {

            $uploadedFiles = UploadService::upload(
                $request->file('media'),
                'posts'
            );

            foreach ($uploadedFiles as $file) {

                PostMedia::create([
                    'schedule_post_id' => $post->id,
                    'file_path' => $file['file_path'],
                    'file_name' => $file['file_name'],
                    'mime_type' => $file['mime_type'],
                    'file_size' => $file['file_size'],
                ]);
            }
        }

        return back()->with(
        'success',
        $request->scheduled_at
            ? 'Post scheduled successfully.'
            : 'Post created successfully.'
    );
    }

    public function publishNow(Request $request)
{
    return 'test';
    $request->validate([
        'message' => 'required|string',
        'channels' => 'required|array|min:1',
    ]);

    foreach ($request->channels as $selectedChannel) {

        $channel = Channel::find($selectedChannel['channel_id']);

        if (!$channel) {
            continue;
        }

        $response = Http::post(
            "https://graph.facebook.com/v25.0/{$channel->channel_id}/feed",
            [
                'message' => $request->message,
                'access_token' => $channel->access_token,
            ]
        );

        $data = $response->json();

        logger()->info('Facebook Response', $data);
    }

    return back()->with(
        'success',
        'Post published successfully.'
    );
}
}
