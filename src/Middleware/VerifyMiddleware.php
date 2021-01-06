<?php


namespace Brackets\Verifications\Middleware;

use Brackets\Verifications\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyMiddleware
{
    /**
     * @var Verification
     */
    private $verification;

    public function __construct(Verification $verification)
    {
        $this->verification = $verification;//app(Verification::class);
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
//
//        if ($this->verification->verify($action, url()->current())) {
//
//            return redirect()->refresh();
//        }

        return $this->verification->verify($action, url()->current(), $next);
    }
}
