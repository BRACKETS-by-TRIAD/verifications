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
        $this->generateCodeAndSend($verifiable, $channel);

        return redirect()->route('brackets/verifications/show', ['redirectTo' => $redirectTo]);
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
