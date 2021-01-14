<?php

namespace Brackets\Verifications\Traits;

use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait VerifiableTrait
{
    protected $activeVerifications;

    protected function loadActiveVerifications()
    {
        if (is_null($this->activeVerifications)) {
            $this->activeVerifications = VerificationCode::where('verifiable_type', get_class($this))
                ->where('verifiable_id', $this->getKey())
                ->where('verifies_until', '>', Carbon::now()->toDateTime())
                ->get();
        }
    }

    public function isVerificationActive(string $action): bool
    {
        $this->loadActiveVerifications();

        return $this->activeVerifications->where('action_name', $action)->count() > 0;
    }

    public function isVerificationEnabled($action): bool
    {
        if (Config::get('verifications.'.$action.'.enabled') == 'forced') {
            return true;
        }

        if (method_exists('isVerificationRequired', $this)) {
            return $this->isVerificationRequired($action);
        } else {
            return false;
        }
    }
}
