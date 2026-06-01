<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use App\Models\SchedulePost;
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

            // return $facebookAccount;

            return Inertia::render('User/Facebook/ConnectPage', [
            'facebookAccount' => $facebookAccount,
            'pages' => $pagesData['data'] ?? [],
        ]);
    }

    // public function connect()
    // {
    //     $facebookAccount = FacebookAccount::where('user_id', 1)->first();

    //     return Inertia::render('User/Facebook/ConnectPage', [
    //         'facebookAccount' => $facebookAccount,
    //     ]);
    // }

    // public function pages()
    // {
    //     $facebookAccount = FacebookAccount::where('user_id', 1)->first();

    //     if (!$facebookAccount) {

    //         return redirect()
    //             ->route('facebook.connect')
    //             ->with(
    //                 'error',
    //                 'Please connect Facebook first.'
    //             );
    //     }

    //     $response = Http::withoutVerifying()->get(
    //         'https://graph.facebook.com/v25.0/me/accounts',
    //         [
    //             'access_token' => $facebookAccount->access_token,
    //             'fields' => 'id,name,access_token,category,picture',
    //         ]
    //     );

    //     $pagesData = $response->json();

    //     if (isset($pagesData['error'])) {

    //         return redirect()
    //             ->route('facebook.connect')
    //             ->with(
    //                 'error',
    //                 'Facebook session expired.'
    //             );
    //     }

    //     return Inertia::render(
    //         'User/Facebook/SelectPage',
    //         [
    //             'pages' => $pagesData['data'] ?? [],
    //         ]
    //     );
    // }
    public function selectedPage(Request $request)
    {
        $request->validate([
            'page_id' => 'required',
            'page_name' => 'required',
            'page_access_token' => 'required',
        ]);
        $facebookAccount = FacebookAccount::where('user_id', 1)->first();

        // Save selected page
        $facebookAccount->update([
            'page_id' => $request->page_id,
            'page_name' => $request->page_name,
            'page_access_token' => $request->page_access_token,
        ]);

        // Fetch posts for selected page
        $response = Http::get(
            "https://graph.facebook.com/v25.0/{$request->page_id}/posts",
            [
                'fields' => 'id,message,created_time',

                'access_token' => $request->page_access_token,
            ]
        );

        $postsData = $response->json();
        return Inertia::render('User/Facebook/SelectPage');
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

        FacebookAccount::updateOrCreate(
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

        // return Inertia::render('User/Dashboard', [
        //     'facebookAccount' => $facebookAccount
        // ]);
        return redirect()->route('facebook.connect')->with('success', 'Facebook connected successfully.');
    }

    // public function getFacebookPages()
    // {
    //     $facebookAccount = FacebookAccount::where('user_id', 1)->first(); // Replace with actual authenticated user ID

    //     if (!$facebookAccount) {
    //         return response()->json(['error' => 'No Facebook account linked'], 404);
    //     }

    //     $response = Http::get('https://graph.facebook.com/v25.0/me/accounts', [
    //         'access_token' => $facebookAccount->access_token,
    //     ]);

    //     $pagesData = $response->json();

    //     return Inertia::render('User/Posts/Index', [
    //         'pages' => $pagesData['data'] ?? []
    //     ]);
    // }


    public function publishPost(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $facebookAccount = FacebookAccount::where('user_id', 1)->first();

        if (!$facebookAccount) {

            return back()->with('error', 'No Facebook account connected.');

        }

        // If user selected future time
        if (
            $request->scheduled_at &&
            now()->lt($request->scheduled_at)
        ) {

            SchedulePost::create([

                'user_id' => 1,

                'facebook_account_id' => $facebookAccount->id,

                'message' => $request->message,

                'scheduled_at' => $request->scheduled_at,

                'status' => 'pending',
            ]);

            return back()->with(
                'success',
                'Post scheduled successfully.'
            );
        }

        // Otherwise publish immediately
        $response = Http::post(
            "https://graph.facebook.com/v25.0/{$facebookAccount->page_id}/feed",
            [
                'message' => $request->message,
                'access_token' => $facebookAccount->page_access_token,
            ]
        );

        $data = $response->json();

        SchedulePost::create([

            'user_id' => 1,

            'facebook_account_id' => $facebookAccount->id,

            'message' => $request->message,

            'published_at' => now(),

            'status' => 'published',

            'facebook_post_id' => $data['id'] ?? null,
        ]);

        return back()->with(
            'success',
            'Post published immediately.'
        );
    }
}
