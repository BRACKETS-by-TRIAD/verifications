<?php

namespace Brackets\Verifications\Facades;

use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool|\Illuminate\Http\RedirectResponse|mixed verify(string $action, string $redirectTo, \Closure $closure = null)
 * @method static bool verifyCode(Verifiable $verifiable, string $action, string $code)
 * @method static bool generateCodeAndSend(string $action)
 * @method static void setUser(Verifiable $verifiable)
 *
 * @see \Brackets\Verifications\Verification
 */
class Verification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'verification';
    }
}
