<?php

namespace Brackets\Verifications\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Brackets\Verifications\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
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

        list($channel, $contact) = array_values($this->getMappings($action_name));

        return View::make("brackets/verifications::verification", [
            'redirectToUrl' => $redirectToUrl,
            'action_name' => $action_name,
            'channel' => $channel,
            'contact' => $contact
        ]);
    }

    private function getMappings(string $action)
    {
        $channel = Config::get('verifications.actions.' .$action. '.channel');
        $contact = '';

        switch ($channel) {
            case 'sms':
                $phoneNumber = Auth::user()->getPhoneAttribute();
                $contact = substr($phoneNumber, 0, 3). substr($phoneNumber, -2);
                break;

            case 'email':
                $email = Auth::user()->getEmailAttribute();
                $contact = substr($email, 0, 3). substr($email, -5);
                break;
        }

        return [
            $channel => trans('brackets/verifications::verifications.' .$channel),
            $contact => $contact
        ];
    }

    public function sendNewCode(Request $request)
    {
        $this->verification->setUser(Auth::user());

        return $this->verification->generateCodeAndSend($request->get('action_name'))
            ? Redirect::back()->with('success', trans('brackets/verifications::verifications.code_resend_success'))
            : Redirect::back()->with('error', trans('brackets/verifications::verifications.code_resend_error'));
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
