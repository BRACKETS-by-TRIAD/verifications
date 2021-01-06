<?php


namespace Brackets\Verifications\Http\Controllers;


use App\Http\Controllers\Controller;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class VerificationController extends Controller
{
    /**
     * @var Verification
     */
    private $verification;

    /**
     * @param Verification $verification
     */
    public function __construct(Verification $verification)
    {
        $this->verification = $verification;
    }

    public function showVerificationForm(Request $request)
    {
        $redirectTo = $request->query('redirectTo');
        $action_name = $request->query('action');

        return View::make("verification", compact($redirectTo, $action_name));
    }

    /**
     * @param Request $request
     * @param Verifiable $verifiable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, Verifiable $verifiable)
    {
        if($this->verification->verifyCode($verifiable, $request->get('action_name'), $request->get('code'))) {
            $request->session()->flash('verifySuccess', [
                'status' => 1,
                'message' => __('verifications.code_verify_success')
            ]);

            return redirect()->route($request->get('redirectTo'));
        }

        $request->session()->flash('verifyFailed', [
            'message' => __('verifications.code_verify_failed')
        ]);

        return redirect()->back();
    }
}
