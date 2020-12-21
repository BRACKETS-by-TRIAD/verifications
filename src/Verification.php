<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Channels\EmailProviderInterface;
use Brackets\Verifications\Channels\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;
use Faker\Provider\Base;
use Illuminate\Support\Str;

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
        $code = $this->generateCode();

        $providers = app()->tagged($this->getChannel($verifiable));

        foreach($providers as $provider) {
            $provider->sendCode($verifiable, $code);
        }

        return $this->repo->createCode($verifiable, $code);
    }

    /**
     * @param Verifiable $verifiable
     * @return string
     */
    private function getChannel(Verifiable $verifiable): string
    {
        $entities = array_merge(config('verifications.default'), config('verifications.2fa'));
        $verifiableEntity = collect($entities)->where('model', get_class($verifiable))->first();

        if($verifiableEntity->channel == 'sms') {
            return SMSProviderInterface::class;
        } else if($verifiableEntity->channel == 'email') {
            return EmailProviderInterface::class;
        } else {
            throw new \InvalidArgumentException('');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateCode(): string
    {
        //TODO: check if generated code doesn't exist ?

        switch(config('verifications.code.type'))
        {
            case 'numeric':
                return strval(Base::randomNumber(config('verifications.code.length', 6), true));

            case 'string':
                return Str::random(config('verifications.code.length', 6));

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }

    public function verifyCode(Verifiable $verifiable, $code): bool
    {
        return $this->repo->verifyCode($verifiable, $code);
    }
}
