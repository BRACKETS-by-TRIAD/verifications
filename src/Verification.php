<?php


namespace Brackets\Verifications;


use Brackets\Verifications\Channels\Contracts\ChannelProviderInterface;
use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;

class Verification
{
    /**
     * @var VerificationCodesRepository
     */
    private $repo;

    /**
     * @var Verifiable
     */
    private $user;

    public function __construct(VerificationCodesRepository $repo)
    {
        $this->repo = $repo;
        $this->user = Auth::user();
    }

    public function verify($action, $redirectTo, \Closure $closure = null)
    {
        if ($this->shouldVerify($action)) {
            $this->generateCodeAndSend($action);

            return redirect()->route('brackets/verifications/show?redirectTo='.$redirectTo);
        }

        if (is_null($closure)) {
            return true;
        }

        return $closure();
    }

    public function shouldVerify($action): bool
    {
        return config('verifications.enabled') && $this->getUser()->isVerificationEnabled($action) && !$this->getUser()->isActionVerifiedAndNonExpired($action);
    }

    /**
     * @param Verifiable $verifiable
     * @return bool
     * @throws \Exception
     */
    public function generateCodeAndSend($action) : bool
    {
        $code = $this->generateCode();

        /** @var ChannelProviderInterface $provider */
        $provider = $this->getProvider($action);

        $this->repo->createCode($this->getUser(), $code);

        $provider->sendCode($this->getUser(), $code);

        return true;
    }

    protected function getProvider($action)
    {
        $verifiableEntity = config('verifications.'.$action);

        if($verifiableEntity->channel == 'sms') {
            return app(SMSProviderInterface::class);
        } else if($verifiableEntity->channel == 'email') {
            return app(EmailProviderInterface::class);
        } else {
            throw new \InvalidArgumentException('');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateCode(): string
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
        return $this->repo->verifyCode($verifiable, $code);
    }

    protected function getUser(): Verifiable
    {
        return $this->user;
    }
}
