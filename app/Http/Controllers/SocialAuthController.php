<?php

namespace App\Http\Controllers;

use App\Http\Services\SocialAccountService;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    public $successStatus = 200;
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }

    public function redirect($social)
    {
        return Socialite::driver($social)->stateless()->redirect();
    }

    public function callback($social)
    {
        $user = SocialAccountService::createOrGetUser(Socialite::driver($social)->stateless()->user(), $social);
        return redirect()->back()->with('message', 'Thành Công');
    }
}
