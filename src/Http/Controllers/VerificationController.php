<?php

namespace Brackets\Verifications\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Brackets\Verifications\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class VerificationController extends BaseController
{
    /** @var Verification */
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
        $redirectToUrl = $request->query('redirectToUrl');
        $action_name = $request->query('action');

        return View::make("brackets/verifications::verification", ['redirectToUrl' => $redirectToUrl, 'action_name' => $action_name]);
    }

    public function sendNewCode(Request $request)
    {
        $this->verification->setUser(Auth::user());

        return $this->verification->generateCodeAndSend($request->get('action_name'))
            ? Redirect::to($request->url())->with('success', trans('brackets/verifications::verifications.code_resend_success'))
            : Redirect::to($request->url())->with('error', trans('brackets/verifications::verifications.code_resend_error'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function verify(Request $request)
    {
        return $this->verification->verifyCode(Auth::user(), $request->get('action_name'), $request->get('code'))
            ? Redirect::to($request->get('redirectToUrl'))->with('success', trans('brackets/verifications::verifications.code_verify_success'))
            : Redirect::back()->with('error', trans('brackets/verifications::verifications.code_verify_failed'));
    }
}
