<?php

namespace App\Http\Services;

use App\Mail\RegisterUser;
use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            $details = [
                'name' => $user->name,
                'email' => $user->email,
                'subject' => 'Bạn đã có tài khoản hãy thử đăng nhập tài khoản ở http://127.0.0.1:8000/signIn',
                'url' => 'http://127.0.0.1:8000/signIn',
                'title' => 'Login'
            ];
            return Mail::to($details['email'])->send(new RegisterUser($details));
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
                    'password' => Hash::make('123456'),
                ]);
            }

            $account->user()->associate($user);
            $account->save();

            return $user;
        }
    }
}
