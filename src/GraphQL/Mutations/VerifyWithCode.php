<?php

namespace Brackets\Verifications\GraphQL\Mutations;

use Brackets\Verifications\Facades\Verification;
use Illuminate\Support\Facades\Auth;

class VerifyWithCode
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

        return Verification::verifyCode($guard->user(), $args['action'], $args['code'])
            ? $guard->user()->createToken('api')->plaintTextToken
            : 'VERIFICATION_FAILED';
    }
}
