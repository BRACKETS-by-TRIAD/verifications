<?php

namespace Brackets\Verifications;

use Brackets\Verifications\Channels\EmailChannel;
use Brackets\Verifications\Channels\SMSChannel;
use Brackets\Verifications\Helpers\CodeGenerator;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;

class Verification
{
    /**
     * @var VerificationCodesRepository
     */
    private $repo;

    /** @var SMSChannel */
    private $SMSChannel;

    public function __construct(VerificationCodesRepository $repo, SMSChannel $SMSChannel)
    {
        $this->repo = $repo;
        $this->SMSChannel = $SMSChannel;
    }

    /**
     * @param Verifiable $verifiable
     * @param String $channel
     * @param String $redirectTo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function verify(Verifiable $verifiable, String $channel, String $redirectTo = '/')
    {
        $perUserConfig = config('verifications.2fa.set_per_user_available')
                            ? auth()->user()->verifiableAttributes()->where('attribute_name', '=', 'login_verification')
                                                                    ->where('attribute_value', true)
                                                                    ->first()
                            : false;

        if(config('verifications.simple_verifications_enabled') || config('verifications.2fa.required_for_all_users') || $perUserConfig) {
            $this->generateCodeAndSend($verifiable, $channel);

            return redirect()->route('brackets/verifications/show', ['redirectTo' => $redirectTo]);
        }

        return redirect()->route($redirectTo);
    }

    /**
     * @param Verifiable $verifiable
     * @param $channel
     * @return mixed
     * @throws \Twilio\Exceptions\TwilioException
     */
    private function generateCodeAndSend(Verifiable $verifiable, $channel)
    {
        $code = CodeGenerator::generateCode();

        switch ($channel)
        {
            case 'sms':
                SMSChannel::sendSmsCode($verifiable, $code);
                return $this->repo->createCode($verifiable, $code);

            case 'email':
                EmailChannel::sendEmailCode($verifiable, $code);
                return $this->repo->createCode($verifiable, $code);

            default:
                throw new \Exception('Unknown channel');
        }
    }

    public function verifyCode(Verifiable $verifiable, $code): bool
    {
        return $this->repo->verifyCode($verifiable, $code);
    }
}
