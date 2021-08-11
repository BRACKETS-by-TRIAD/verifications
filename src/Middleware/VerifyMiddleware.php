<?php

namespace Brackets\Verifications\Middleware;

use Brackets\Verifications\Verification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerifyMiddleware
{
    /**  @var Verification */
    private $verification;

    public function __construct(Verification $verification)
    {
        $this->verification = $verification;
    }

    /**
     * @param Request $request
     * @param \Closure $next
     * @param $params
     * @throws \Exception
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, \Closure $next, $params)
    {
        list($action) = explode(":", $params);

        if ($request->isMethod('post')) {
            $response = $this->verification->verify($action, URL::previous(), $request->get('template'));
        } else {
            $response = $this->verification->verify($action, $request->url(), $request->query('template'));
        }

        if ($response !== true) {
            return $response;
        }

        return $next($request);
    }
}
