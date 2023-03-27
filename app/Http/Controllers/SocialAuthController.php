<?php

namespace App\Http\Controllers;

use App\Http\Services\SocialAccountService;
use Laravel\Socialite\Facades\Socialite;

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
        return SocialAccountService::createOrGetUser(Socialite::driver($social)->stateless()->user(), $social);
    }
}
