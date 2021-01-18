<?php

namespace Brackets\Verifications\Traits;

use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

trait VerifiableTrait
{
    /** @var Collection */
    protected $activeVerifications;

    protected function loadActiveVerifications(string $action)
    {
        $newVerifications = $this->shouldKeepDuringSession($action)
            ? VerificationCode::allFor($this)->whereNull('verifies_until')->whereNotNull('used_at')->get()
            : VerificationCode::allFor($this)->where('verifies_until', '>', Carbon::now()->toDateTime())->get();

        $this->activeVerifications = is_null($this->activeVerifications)
            ? $newVerifications
            : $this->activeVerifications->merge($newVerifications);
    }

    private function shouldKeepDuringSession(string $action): bool
    {
        return (Config::get('verifications.actions.'.$action.'.keep_verified_during_session') === true)
            && Session::has('last_activity')
            && (Session::get('last_activity') < Carbon::now()->addMinutes(Config::get('session.lifetime'))->toDateTime());
    }

    public function isVerificationActive(string $action): bool
    {
        $this->loadActiveVerifications($action);

        return $this->activeVerifications->where('action_name', $action)->count() > 0;
    }

    public function isVerificationEnabled(string $action): bool
    {
        if (Config::get('verifications.actions.'.$action.'.enabled')) {
            return true;
        }

        if (method_exists('isVerificationRequired', $this)) {
            return $this->isVerificationRequired($action);
        }

        return false;
    }
}
