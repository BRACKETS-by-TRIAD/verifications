<?php

namespace Brackets\Verifications\Http\Controllers;

use Brackets\Verifications\Facades\Verification;
use Illuminate\Contracts\View\View as ViewInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class VerificationController extends BaseController
{
    public function showVerificationForm(Request $request): ViewInterface
    {
        $redirectToUrl = $request->query('redirectToUrl');
        $action_name = $request->query('action');
        $template = Config::get('verifications.actions.' .$action_name. '.form_template', 'brackets/verifications::verification');

        list($channel, $contact) = array_values($this->getMappings($action_name));

        return View::make($template, [
            'redirectToUrl' => $redirectToUrl,
            'action_name' => $action_name,
            'channel' => $channel,
            'contact' => $contact
        ]);
    }

    private function getMappings(string $action): array
    {
        $channel = Config::get('verifications.actions.' .$action. '.channel');
        $contact = '';

        switch ($channel) {
            case 'sms':
                $contact = $this->getMutedString(Auth::user()->phone);
                break;

            case 'email':
                $contact = $this->getMutedString(Auth::user()->email, 3, 5);
                break;
        }

        return [
            $channel => trans('brackets/verifications::verifications.' .$channel),
            $contact => $contact
        ];
    }

    private function getMutedString(string $str, $firstCharactersCount = 3, $lastCharactersCount = 2): string
    {
        $characters = str_split($str);
        $result = '';

        foreach ($characters as $key => $character) {
            if ($key < $firstCharactersCount || $key > count($characters) - $lastCharactersCount) {
                $result = $result . strval($character);
            } else {
                $result = $result . '*';
            }
        }

        return $result;
    }

    public function sendNewCode(Request $request): RedirectResponse
    {
        Verification::setUser(Auth::user());

        return Verification::generateCodeAndSend($request->get('action_name'), $request->ip(), $request->userAgent())
            ? Redirect::back()->with('success', trans('brackets/verifications::verifications.code_resend_success'))
            : Redirect::back()->with('error', trans('brackets/verifications::verifications.code_resend_error'));
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @return mixed
     */
    public function verify(Request $request)
    {
        return Verification::verifyCode(Auth::user(), $request->get('action_name'), $request->get('code'))
            ? Redirect::to($request->get('redirectToUrl'))->with('success', trans('brackets/verifications::verifications.code_verify_success'))
            : Redirect::back()->with('error', trans('brackets/verifications::verifications.code_verify_failed'));
    }
}
