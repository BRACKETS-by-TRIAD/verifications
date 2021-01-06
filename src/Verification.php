<?php

namespace Brackets\Verifications;


use Brackets\Verifications\Channels\Contracts\ChannelProviderInterface;
use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

    /**
     * @param string $action
     * @param $redirectTo
     * @param \Closure|null $closure
     * @return bool|mixed
     * @throws \Exception
     */
    public function verify(string $action, $redirectTo, \Closure $closure = null)
    {
        if ($this->shouldVerify($action)) {
            $this->generateCodeAndSend($action);

            return redirect()->route('brackets/verifications/show?action='. $action .'&redirectTo='.$redirectTo);
        }

        return is_null($closure) ? true : $closure();
    }

    public function shouldVerify(string $action): bool
    {
        return Config::get('verifications.enabled')
            && $this->getUser()->isVerificationEnabled($action)
            && !$this->getUser()->isActionVerifiedAndNonExpired($action);
    }

    /**
     * @param string $action
     * @return bool
     * @throws \Exception
     */
    public function generateCodeAndSend(string $action) : bool
    {
        $code = $this->generateCode();

        /** @var ChannelProviderInterface $provider */
        $provider = $this->getProvider($action);

        $this->repo->createCode($this->getUser(), $action, $code);

        $provider->sendCode($this->getUser(), $code);

        return true;
    }

    /**
     * @param string $action
     * @return mixed
     */
    protected function getProvider(string $action)
    {
        $verifiableEntity = Config::get('verifications.'.$action);

        if($verifiableEntity->channel == 'sms') {
            return app(SMSProviderInterface::class);
        } else if($verifiableEntity->channel == 'email') {
            return app(EmailProviderInterface::class);
        } else {
            throw new \InvalidArgumentException('Unsupported channel type.');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateCode(): string
    {
        //TODO: check if generated code doesn't exist ?

        switch(Config::get('verifications.code.type'))
        {
            case 'numeric':
                $nbDigits = Config::get('verifications.code.length', 6);
                $max = 10 ** $nbDigits - 1;
                return strval(mt_rand(10 ** ($nbDigits - 1), $max));

            case 'string':
                return Str::random(Config::get('verifications.code.length', 6));

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }

    public function verifyCode(Verifiable $verifiable, string $action, string $code): bool
    {
        return $this->repo->verifyCode($verifiable, $action, $code);
    }

    protected function getUser(): Verifiable
    {
        return $this->user;
    }
}
