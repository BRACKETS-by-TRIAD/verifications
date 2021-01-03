<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Models\Verifiable;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyMiddleware
{
    /**
     * @var Verification
     */
    private $verification;

    public function __construct()
    {
        $this->verification = new Verification();
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
        list($modelName, $action) = explode(":", $params);

        if($this->shouldVerify($request, $action)) {

            $this->verification->generateCodeAndSend($request->route()->bindingFields()[0]);

            return redirect('brackets/verifications/show');
        }

        return $next($request);
    }

    private function shouldVerify(Request $request, $action): bool
    {
        return ($request->session()->get($action.'valid_until') < Carbon::now())
                || !auth()->user()->hasVerification($action);
    }
}
