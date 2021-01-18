<?php

namespace Brackets\Verifications\Middleware;

use Brackets\Verifications\Verification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
     * @return RedirectResponse|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(Request $request, \Closure $next, $params)
    {
        list($action) = explode(":", $params);

        if (Config::get('verifications.actions.'. $action .'.keep_verified_during_session')) {
            Session::put('last_activity', Carbon::now()->toDateTime());
        }

        $response = $this->verification->verify($action, $request->url());

        return $response instanceof RedirectResponse ? $response : $next($request);
    }
}
