<?php

namespace Verifications\GraphQL\Mutations;

use Brackets\Verifications\Facades\Verification;
use GraphQL\Error\Error;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class LoginWithCredentials
{
    /**
     * @param null $_
     * @param array<string, mixed> $args
     * @throws Error
     * @return string
     */
    public function __invoke($_, array $args)
    {
        $guard = Auth::guard('admin');

        if (!$guard->attempt($args)) {
            throw new Error(__('Invalid credentials.'));
        }

        return (Verification::verify($args['action'], URL::previous()) instanceof RedirectResponse)
            ? 'VERIFICATION_REQUIRED'
            : $guard->user()->createToken('api')->plaintTextToken;
    }
}
