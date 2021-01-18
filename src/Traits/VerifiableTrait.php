<?php

namespace Brackets\Verifications\Traits;

use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

trait VerifiableTrait
{
    protected $activeVerifications;

    //TODO: needs some refactor
    protected function loadActiveVerifications(string $action)
    {
        if (Config::get('verifications.actions.'.$action.'.keep_verified_during_session') === true) {
            if (Session::has('last_activity') && Session::get('last_activity') < Carbon::now()->addMinutes(Config::get('session.lifetime'))->toDateTime()) {
                $this->activeVerifications += VerificationCode::allFor($this)->whereNull('verifies_until')->whereNotNull('used_at')->get();
            } else {
                $this->activeVerifications = collect([]);       // if session expired, user should verify action again
            }
        }
        else {
            $this->activeVerifications += VerificationCode::allFor($this)->where('verifies_until', '>', Carbon::now()->toDateTime())->get();
        }
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
