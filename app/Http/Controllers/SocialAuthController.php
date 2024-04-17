<?php

namespace App\Http\Controllers;

use App\Classes\Response;
use App\Http\Resources\UserResource;
use App\Http\Services\Auth\AuthService;
use App\Http\Services\SocialAccountService;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public $successStatus = 200;
    protected $authService;
    protected $socialAccountService;
    public function __construct(AuthService $authService, SocialAccountService $socialAccountService)
    {
        $this->authService = $authService;
        $this->socialAccountService = $socialAccountService;
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);

    }

    public function redirect($social)
    {
        return Socialite::driver($social)->redirect()->getTargetUrl();
    }

    public function callback($social)
    {
        $user = $this->socialAccountService->createOrGetUser(Socialite::driver($social)->stateless()->user(), $social);
        $token = $this->authService->createUserToken($user);
        $tokenInfo = $token->token;
        auth()->login($user);
        $data =  [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($tokenInfo->expires_at)->toDateTimeString(),
            'user' => new UserResource($user),
        ];
        $response = response()->json($data);
        return $response->withCookie(cookie(
            env('AUTH_COOKIE_NAME', 'login'),
            $data['access_token'],
            env('COOKIE_LIFETIME', 60 * 24 * 7),
            null,
            null,
            true,
            false,
            false,
            'None'
        ));
        // return redirect('http://localhost:8083/login/callback');
        // return response($script);
    }
}
