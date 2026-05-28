<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FacebookAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class FacebookController extends Controller
{
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
            ]);
        $facebookAuthUrl = "https://www.facebook.com/v25.0/dialog/oauth?$query";
        return redirect($facebookAuthUrl);
    }

    public function handleFacebookCallback(Request $request)
    {
        if ($request->state !== session('facebook_state')) {
            abort(403, 'Invalid state');
        }

        $response = Http::get('https://graph.facebook.com/v25.0/oauth/access_token', [
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
        $userResponse = Http::get('https://graph.facebook.com/me', [
            'fields' => 'id,name,email,picture',
            'access_token' => $accessToken,
        ]);

        $userData = $userResponse->json();

        $facebookAccount = FacebookAccount::create([
            'user_id' => 1, // Replace with actual authenticated user ID
            'facebook_id' => $userData['id'] ?? null,
            'name' => $userData['name'] ?? null,
            'email' => $userData['email'] ?? null,
            'avatar' => $userData['picture']['data']['url'] ?? null,
            'access_token' => $accessToken,
        ]);

        return Inertia::render('User/Dashboard', [
            'facebookAccount' => $facebookAccount
        ]);
    }
}
