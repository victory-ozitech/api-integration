<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\FacebookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class FacebookAuthController extends Controller
{
    public function connect()
    {
        $facebookAccount = FacebookAccount::where('user_id', 1)->first();

        if (!$facebookAccount) {
            return Inertia::render('User/Facebook/ConnectPage', [
                'facebookAccount' => null,
                'pages'           => [],
            ]);
        }

        $response = $this->facebookGet('me/accounts', [
            'access_token' => $facebookAccount->access_token,
            'fields'       => 'id,name,access_token,category,picture',
        ]);

        $pagesData = $response->json();

        if (isset($pagesData['error'])) {
            $facebookAccount->delete();
            return redirect()->route('facebook.connect');
        }

        return Inertia::render('User/Facebook/ConnectPage', [
            'facebookAccount' => $facebookAccount,
            'pages'           => $pagesData['data'] ?? [],
        ]);
    }

    public function redirectToFacebook()
    {
        $state = csrf_token();
        session(['facebook_state' => $state]);

        $query = http_build_query([
            'client_id'     => config('services.facebook.app_id'),
            'redirect_uri'  => config('services.facebook.redirect'),
            'state'         => $state,
            'scope'         => implode(',', [
                'email',
                'public_profile',
                'pages_manage_posts',
                'pages_read_engagement',
                'pages_show_list',
                'pages_manage_engagement',
            ]),
            'response_type' => 'code',
            'auth_type'     => 'rerequest',
        ]);

        return redirect("https://www.facebook.com/v25.0/dialog/oauth?{$query}");
    }

    public function handleCallback(Request $request)
    {
        if ($request->state !== session('facebook_state')) {
            abort(403, 'Invalid state');
        }

        $tokenResponse = $this->facebookGet('oauth/access_token', [
            'client_id'     => config('services.facebook.app_id'),
            'client_secret' => config('services.facebook.app_secret'),
            'redirect_uri'  => config('services.facebook.redirect'),
            'code'          => $request->code,
        ]);

        $tokenData = $tokenResponse->json();

        if (!isset($tokenData['access_token'])) {
            return response()->json($tokenData, 400);
        }

        $accessToken = $tokenData['access_token'];

        $userResponse = $this->facebookGet('me', [
            'fields'       => 'id,name,email,picture',
            'access_token' => $accessToken,
        ]);

        $userData = $userResponse->json();

        $facebookAccount = FacebookAccount::updateOrCreate(
            ['facebook_id' => $userData['id']],
            [
                'user_id'      => 1,
                'name'         => $userData['name'] ?? null,
                'email'        => $userData['email'] ?? null,
                'avatar'       => $userData['picture']['data']['url'] ?? null,
                'access_token' => $accessToken,
            ]
        );

        $pagesResponse = $this->facebookGet('me/accounts', [
            'access_token' => $accessToken,
            'fields'       => 'id,name,picture,access_token',
        ]);

        $pages = $pagesResponse->json()['data'] ?? [];

        foreach ($pages as $page) {
            Channel::updateOrCreate(
                [
                    'user_id'    => 1,
                    'channel_id' => $page['id'],
                ],
                [
                    'facebook_account_id' => $facebookAccount->id,
                    'platform'            => 'facebook',
                    'channel_name'        => $page['name'],
                    'access_token'        => $page['access_token'],
                    'avatar'              => $page['picture']['data']['url'] ?? null,
                ]
            );
        }

        return redirect()->route('facebook.connect')
            ->with('success', 'Facebook connected successfully.');
    }

    public function selectedPage(Request $request)
    {
        $channel = Channel::findOrFail($request->channel_id);

        session(['selected_channel_id' => $channel->id]);

        return back()->with('success', 'Page selected successfully.');
    }

    // Centralizes Http::withoutVerifying() in one place
    private function facebookGet(string $endpoint, array $params)
    {
        $base = str_starts_with($endpoint, 'oauth')
            ? 'https://graph.facebook.com/v25.0/'
            : 'https://graph.facebook.com/v25.0/';

        return Http::withoutVerifying()->get($base . $endpoint, $params);
    }
}