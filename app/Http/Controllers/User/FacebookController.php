<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        $appId = env('FACEBOOK_APP_ID');
        $redirectUri = env('FACEBOOK_REDIRECT_URI');
        $scope = implode(',', ['email', 'public_profile']);
        $facebookAuthUrl = "https://www.facebook.com/v10.0/dialog/oauth?client_id={$appId}&redirect_uri={$redirectUri}&scope={$scope}";
        return redirect($facebookAuthUrl);
    }
}
