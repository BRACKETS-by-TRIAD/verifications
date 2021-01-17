<?php

namespace Brackets\Verifications\Traits;

use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait VerifiableTrait
{
    protected $activeVerifications;

    protected function loadActiveVerifications(string $action)
    {
        if (is_null($this->activeVerifications)) {
            $this->activeVerifications = VerificationCode::where('verifiable_type', get_class($this))
                                            ->where('verifiable_id', $this->getKey())
                                            ->where(function($q) use ($action) {
                                                if (!Config::get('verifications.'.$action.'.keep_verified_during_session')) {
                                                    $q->where('verifies_until', '>', Carbon::now()->toDateTime());
                                                }
                                            })
                                            ->get();
        }
    }

    public function isVerificationActive(string $action): bool
    {
        $this->loadActiveVerifications($action);

        return $this->activeVerifications->where('action_name', $action)->count() > 0;
    }

    public function isVerificationEnabled(string $action): bool
    {
        if (Config::get('verifications.'.$action.'.enabled')) {
            return true;
        }

        if (method_exists('isVerificationRequired', $this)) {
            return $this->isVerificationRequired($action);
        }

        return false;
    }
}
