<?php

namespace Brackets\Verifications\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Session;

class OnLogoutListener
{
    public function handle(Logout $event)
    {
        Session::forget('last_activity');
    }
}