<?php

namespace App\Listeners;

use App\Events\SetRemember;
use Carbon\Carbon;
use Laravel\Passport\Passport;

class SetTokenExpiration
{
    public function handle(SetRemember $event)
    {
        if ($event->remember) {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addYears(100));
        } else {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(1));
        }
    }
}
