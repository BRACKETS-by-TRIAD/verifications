<?php

namespace Brackets\Verifications;

use Brackets\Verifications\Channels\EmailProviderInterface;
use Brackets\Verifications\Helpers\CodeGenerator;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;

class Verification
{
    /**
     * @var VerificationCodesRepository
     */
    private $repo;

    public function __construct(VerificationCodesRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Verifiable $verifiable
     * @param String $redirectTo
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function verify(Verifiable $verifiable, String $redirectTo = '/')
    {
        if(config('verifications.enabled') && $this->shouldVerify($verifiable)) {
            $this->generateCodeAndSend($verifiable);

            return redirect()->route('brackets/verifications/show', ['redirectTo' => $redirectTo]);
        }

        return redirect()->route($redirectTo);
    }

    private function shouldVerify(Verifiable $verifiable): bool
    {
        $entities = array_merge(config('verifications.default'), config('verifications.2fa'));

        foreach($entities as $entity) {
            if($verifiable instanceof $entity['model']) {
                return ($entity['enabled'] == 'forced') || ($entity['enabled'] == 'optional' && auth()->user()->login_verification);
            }
        }

        return false;
    }

    /**
     * @param Verifiable $verifiable
     * @return mixed
     * @throws \Exception
     */
    private function generateCodeAndSend(Verifiable $verifiable)
    {
        $code = CodeGenerator::generateCode();  //todo

        $provider = resolve(EmailProviderInterface::class);
        $provider->sendCode($verifiable, $code);

        return $this->repo->createCode($verifiable, $code);
    }

    public function verifyCode(Verifiable $verifiable, $code): bool
    {
        return $this->repo->verifyCode($verifiable, $code);
    }
}
