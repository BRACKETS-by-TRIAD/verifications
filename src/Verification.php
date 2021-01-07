<?php

namespace Brackets\Verifications;

use Brackets\Verifications\Channels\Contracts\ChannelProviderInterface;
use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
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
     * @param string $redirectTo
     * @param \Closure|null $closure
     * @return bool|\Illuminate\Http\RedirectResponse|mixed
     * @throws \Exception
     */
    public function verify(string $action, string $redirectTo, \Closure $closure = null)
    {
        if ($this->shouldVerify($action)) {
            $this->generateCodeAndSend($action);

            return Redirect::route('brackets/verifications/show', ['action' => $action, 'redirectToUrl' => $redirectTo]);
        }

        return is_null($closure) ? true : $closure();
    }

    /**
     * @param Verifiable $verifiable
     * @param string $action
     * @param string $code
     * @return bool
     */
    public function verifyCode(Verifiable $verifiable, string $action, string $code): bool
    {
        return $this->repo->verifyCode($verifiable, $action, $code);
    }

    private function shouldVerify(string $action): bool
    {
        return Config::get('verifications.enabled')
            && $this->getUser()->isVerificationEnabled($action)
            && !$this->getUser()->isVerificationActive($action);
    }

    /**
     * @param string $action
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function generateCodeAndSend(string $action) : bool
    {
        $code = $this->generateCode($action);

        /** @var ChannelProviderInterface $provider */
        $provider = $this->getProvider($action);

        $this->repo->createCode($this->getUser(), $action, $code);

        $provider->sendCode($this->getUser(), $code);

        return true;
    }

    /**
     * @param string $action
     * @return mixed|object
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function getProvider(string $action)
    {
        $channel = Config::get('verifications.actions.'.$action .'.channel');

        switch ($channel) {
            case 'sms':
                return Container::getInstance()->make(SMSProviderInterface::class);
            case 'email':
                return Container::getInstance()->make(EmailProviderInterface::class);
            default:
                throw new \InvalidArgumentException('Unsupported channel type.');
        }
    }

    /**
     * TODO: move to separate class + check if generated code doesn't exist ?
     *
     * @param string $action
     * @return string
     * @throws \Exception
     */
    protected function generateCode(string $action): string
    {
        $codeType = Config::get('verifications.actions.'. $action .'.code.type');
        $codeLength = Config::get('verifications.actions.'. $action .'.code.length', 6);

        switch ($codeType) {
            case 'numeric':
                $nbDigits = $codeLength;
                $max = 10 ** $nbDigits - 1;

                return strval(mt_rand(10 ** ($nbDigits - 1), $max));

            case 'string':
                return Str::random($codeLength);

            default:
                throw new \Exception('Verification code type not specified!');
        }
    }

    /**
     * @return Verifiable
     */
    private function getUser(): Verifiable
    {
        return $this->user;
    }
}
