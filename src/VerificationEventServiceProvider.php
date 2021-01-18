<?php

namespace Brackets\Verifications;

use Brackets\Verifications\Listeners\OnLogoutListener;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\EventServiceProvider;

class VerificationEventServiceProvider extends EventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Logout::class => [
            OnLogoutListener::class
        ]
    ];
}