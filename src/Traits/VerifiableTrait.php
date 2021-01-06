<?php

namespace Brackets\Verifications\Traits;


use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

trait VerifiableTrait
{
    public static function bootVerifiableTrait()
    {
        (new static)->loadVerificationsForUser();
    }

    protected function loadVerificationsForUser()
    {
        $usersVerifications = VerificationCode::where('verifiable_type', get_class($this))
                                              ->where('verifiable_id', $this->getKey())        //FIXME: doesn't work with AdminUser??
                                              ->where('verifies_until', '>', Carbon::now()->toDateTime())
                                              ->get();

        Session::put('verifications', $usersVerifications);
    }

    public function isActionVerifiedAndNonExpired(string $action): bool
    {
        $actionSessions = Session::get('verifications')->filter(function($item) use ($action) {
            return (data_get($item, 'action') == $action) && (Carbon::parse(data_get($item, 'verifies_until')) > Carbon::now());
        });

        return count($actionSessions) > 0;
    }

    public function isVerificationEnabled($action): bool
    {
        if (Config::get('verifications.'.$action.'.enabled') == 'forced') {
            return true;
        }

        if (Config::get('verifications.'.$action.'.enabled') == 'optional') {       //TODO: use-case needs to be discussed
            if (method_exists('isVerificationRequired', $this)) {
                return $this->isVerificationRequired($action);
            } elseif ($this->{$action}) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
