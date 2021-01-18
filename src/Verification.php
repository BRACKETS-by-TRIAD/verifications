<?php

namespace Brackets\Verifications;

use Brackets\Verifications\Channels\Contracts\ChannelProviderInterface;
use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\CodeGenerator\Contracts\GeneratorInterface;
use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Repositories\VerificationCodesRepository;
use Carbon\Carbon;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Verification
{
    /** @var VerificationCodesRepository */
    private $repo;

    /**  @var Verifiable */
    private $user;

    /** @var GeneratorInterface */
    private $generator;

    public function __construct(VerificationCodesRepository $repo, GeneratorInterface $generator)
    {
        $this->repo = $repo;
        $this->user = Auth::user();
        $this->generator = $generator;
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

        if (Config::get('verifications.actions.'. $action .'.keep_verified_during_session')) {
            Session::put('last_activity', Carbon::now()->toDateTime());
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
            && !is_null($this->getUser())
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

    protected function generateCode(string $action): string
    {
        $codeType = Config::get('verifications.actions.'. $action .'.code.type');
        $codeLength = Config::get('verifications.actions.'. $action .'.code.length', 6);

        return $this->generator->generate($codeType, $codeLength);

    }

    /**
     * @return Verifiable
     */
    private function getUser(): Verifiable
    {
        return $this->user;
    }

    /**
     * @param Verifiable $verifiable
     */
    public function setUser(Verifiable $verifiable): void
    {
        $this->user = $verifiable;
    }
}
