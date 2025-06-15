<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class LoginEventListener
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (is_null($user->email_verified_at)) {
            Session::flash('unverified', true);
        } else {
            Session::forget('unverified');
        }
    }
}
