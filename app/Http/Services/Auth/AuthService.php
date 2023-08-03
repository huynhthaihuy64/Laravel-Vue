<?php

namespace App\Http\Services\Auth;

use App\Events\SetRemember;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class AuthService
{
    protected $user;

    /**
     * __construct
     *
     * @param  mixed $repository
     * @return void
     */

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login($request = [],$remember)
    {
        $user = $this->user->where('email', $request['email'])->first();
        if ($user === null || !Auth::attempt($request)) {
            return $this->_throwErrorAuthenticate(__('Login Failed'));
        }
        return $this->_processAfterLogin($user,$remember);
    }

    /**
     * Process data after login
     *
     * @param  \App\Models\User  $user
     *
     * @return array|\Illuminate\Auth\AuthenticationException
     */
    private function _processAfterLogin(User $user, $remember): array
    {
        event(new SetRemember($remember));
        $dataUser = new UserResource($user);
        $token = $this->createUserToken($user);
        $tokenInfo = $token->token;
        return [
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_in' => Carbon::parse($tokenInfo->expires_at)->toDateTimeString(),
            'user' => $dataUser,
        ];
    }

    /**
     * Create token for login user
     *
     * @param $request
     *
     * @return PersonalAccessTokenResult| bool
     */
    public function createUserToken($user)
    {
        // Nếu dùng Sanctum:
        // if($remember == true) {
        //     $duration = Carbon::now()->addMinutes(config('sanctum.rm_expiration'));
        // } else {
        //     $duration = Carbon::now()->addMinutes(config('sanctum.expiration'));
        // }
        // $token = $user->createToken($user->email,['*'],$duration);
        // $tokenResult['access_token'] = $token->plainTextToken;
        // $tokenResult['expires_at'] = $token->accessToken->expires_at;
        $oldTokens = $user->token();
        if ($oldTokens !== null) {
            $oldTokens->revoke();
        }

        $tokenResult = $user->createToken($user->email);
        return $tokenResult;
    }

    /**
     * Logout
     *
     * @return boolean
     */
    public function logout(): bool
    {
        // Nếu dung sanctum:
        // try {
        //     $user = auth()->user();
        //     $user->currentAccessToken()->delete();
        //     return true;
        // } catch (\Exception $e) {
        //     throw $e;
        // }
        try {
            $user = Auth::user();
            $user->token()->revoke();
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Throw Error Authenticate function
     *
     * @param [type] $message
     * @param  integer  $code
     *
     * @return \Illuminate\Auth\AuthenticationException;
     */
    private function _throwErrorAuthenticate($message = null, $code = 401)
    {
        if ($message) {
            throw new AuthenticationException($message);
        }
        throw new AuthenticationException('Login Failed');
    }

    // public function changePassword($request)
    // {
    //     $user = Auth::user();
    //     if ((Hash::check($request->old_password, Auth::user()->password)) == false) {
    //         throw new \Exception('Check your old password.');
    //     } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
    //         throw new \Exception('Please enter a password which is not similar then current password.');
    //     } else {
    //         return $this->user->where('id', $user->id)->update(['password' => Hash::make($request->new_password)]);
    //     }
    // }

    // public function resetPasswordToken($request)
    // {
    //     $user = $this->user->where('email', $request->email)->first();
    //     if ($user) {
    //         DB::beginTransaction();
    //         try {
    //             $user->update([
    //                 'password' => Hash::make($request->password),
    //                 'password_hint' => $request->password_hint,
    //             ]);
    //             $mail = Mail::to($request['email'])->send(new ResetPasswordMail([
    //                 'username' => $user->username,
    //                 'email' => $request->email,
    //                 'password' => $request->password,
    //             ]));
    //             DB::commit();
    //             return __('messages.reset_password.success');
    //         } catch (\Exception $e) {
    //             DB::rollback();
    //             throw new \Exception(__('messages.reset_password.fail'));
    //         }
    //     }
    //     throw new \Exception(__('messages.user.email.exists'));
    // }

    // public function resetPassword($request)
    // {
    //     return DB::table('password_resets')->where('token', $request->token)->first();
    // }

    // public function forgotPassword($request)
    // {
    //     if (DB::table('password_resets')->where('token', $request->token)->where('email', $request->email)->first()) {
    //         DB::beginTransaction();
    //         try {
    //             DB::table('password_resets')->where('token', $request->token)->where('email', $request->email)->delete();
    //             $update = $this->user->where('email', $request->email)->update(['password' => Hash::make($request->password)]);
    //             DB::commit();
    //             return $update;
    //         } catch (\Exception $e) {
    //             throw new \Exception($e->getMessage());
    //             DB::rollback();
    //         }
    //     }
    //     throw new \Exception('New password creation failed');
    // }
}