<?php

namespace App\Http\Services;

use App\Mail\RegisterUser;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class SocialAccountService
{
    public static function createOrGetUser(ProviderUser $providerUser, $social)
    {
        $account = SocialAccount::whereProvider($social)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            $user = $account->user;
            if(!($user->provider)) {
                $user->update([
                    'provider' => $social
                ]);
            }
            // $details = [
            //     'name' => $user->name,
            //     'email' => $user->email,
            //     'subject' => 'Bạn đã có tài khoản hãy thử đăng nhập tài khoản ở env('APP_URL').'/signIn',
            //     'url' => env('APP_URL').'/signIn',
            //     'title' => 'Login'
            // ];
            // Mail::to($details['email'])->send(new RegisterUser($details));
            // return response()->json(['message' => __('messages.mail.send.email')]);
            return $user;
        } else {
            $email = $providerUser->getEmail() ?? $providerUser->getNickname();
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $social
            ]);
            $user = User::whereEmail($email)->first();

            if (!$user) {

                $user = User::create([
                    'email' => $email,
                    'name' => $providerUser->getName(),
                    'password' => Hash::make('12345678'),
                    'provider' => $social
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            $details = [
                'name' => $user->name,
                'email' => $user->email,
                'subject' => 'Bạn đã tạo tài khoản thành công hãy thử đăng nhập với password là 12345678',
                'url' => env('APP_URL').'/signIn',
                'title' => 'Login'
            ];
            Mail::to($details['email'])->send(new RegisterUser($details));
            // return response()->json(['message' => __('messages.mail.send.email')]);
            return $user;
        }
    }
}
