<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;
use Illuminate\Support\Str;

class Verification
{
    /**
     * @var VerificationCodesRepository
     */
    private $repo;
    /**
     * @var \Closure
     */
    private $closure;

    public function __construct(VerificationCodesRepository $repo)
    {
        $this->repo = $repo;
    }

    //akcia kt. je zabezpecena, sa zavola 2x, (user napr klikne 2x na download faktury)

    public function verify(Verifiable $verifiable, \Closure $closure)
    {
        if(config('verifications.enabled') && $this->shouldVerify($verifiable)) {
            $this->generateCodeAndSend($verifiable);
            $this->closure = $closure;

            return redirect()->route('brackets/verifications/show');        //sem dat redirect route ako parameter
        }

        return $closure();
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
    public function generateCodeAndSend(Verifiable $verifiable)
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
                $nbDigits = config('verifications.code.length', 6);
                $max = 10 ** $nbDigits - 1;
                return strval(mt_rand(10 ** ($nbDigits - 1), $max));

            case 'string':
                return Str::random(config('verifications.code.length', 6));

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }

    public function verifyCode(Verifiable $verifiable, $code): bool
    {
        $isVerified = $this->repo->verifyCode($verifiable, $code);

        if($isVerified) {
            ($this->closure)();
            $this->closure = null;
        }

        return $isVerified;
    }
}
