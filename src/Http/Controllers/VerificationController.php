<?php


namespace Brackets\Verifications\Http\Controllers;


use App\Http\Controllers\Controller;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Verification;
use Illuminate\Http\Request;

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

    public function showVerificationForm($redirectTo)
    {
        return view("verification", compact($redirectTo));
    }

    /**
     * @param Request $request
     * @param Verifiable $verifiable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request, Verifiable $verifiable)
    {
        if($this->verification->verifyCode($verifiable, $request->get('code'))) {
            $request->session()->flash('verifySuccess', [
                'status' => 1,
                'message' => __('verifications.code_verify_success')
            ]);

            return redirect()->to($request->get('redirectTo'));
        }

        $request->session()->flash('verifyFailed', [
            'message' => __('verifications.code_verify_failed')
        ]);

        return redirect()->back();
    }
}
