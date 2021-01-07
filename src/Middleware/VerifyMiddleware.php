<?php

namespace Brackets\Verifications\Middleware;


use Brackets\Verifications\Verification;
use Illuminate\Http\Request;

class VerifyMiddleware
{
    /**
     * @var Verification
     */
    private $verification;

    public function __construct(Verification $verification)
    {
        $this->verification = $verification;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, $params)
    {
        list($action) = explode(":", $params);

        $response = $this->verification->verify($action, $request->url());

        if ($response !== true) {
            return $response;
        }

        return $next($request);
    }
}
