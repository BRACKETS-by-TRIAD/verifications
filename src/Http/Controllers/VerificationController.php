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
        $redirectToUrl = $request->query('redirectToUrl');
        $action_name = $request->query('action');

        return View::make("brackets/verifications::verification", ['redirectToUrl' => $redirectToUrl, 'action_name' => $action_name]);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function verify(Request $request)
    {
        if ($this->verification->verifyCode(Auth::user(), $request->get('action_name'), $request->get('code'))) {
            $request->session()->flash('verifySuccess', [
                'status' => 1,
                'message' => trans('brackets/verifications::verifications.code_verify_success')
            ]);

            return Redirect::to($request->get('redirectToUrl'));
        }

        $request->session()->flash('verifyFailed', [
            'message' => trans('brackets/verifications::verifications.code_verify_failed')
        ]);

        return Redirect::back();
    }
}
