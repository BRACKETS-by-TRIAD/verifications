<?php


namespace Brackets\Verifications\Traits;


use Brackets\Verifications\Models\Verifiable;

trait VerifiableTrait
{

    public function bootVerifiableTrait()
    {
        $this->loadVerificationsForUser();
    }

    public function isActionVerifiedAndNonExpired($action): bool
    {
        return Session::has('verifications'.$action) && Carbon::parse(Session::get('verifications'.$action.'.expires_at')) > Carbon::now();
    }

    protected function loadVerificationsForUser()
    {
        if (!Session::has('verifications')) {
            // TODO loadnut z DB to pole
            // Session::put()
        }
    }

    public function isVerificationEnabled($action): bool
    {
        if (config('verifications.'.$action.'.enabled') == 'forced') {
            return true;
        }

        if (config('verifications.'.$action.'.enabled') == 'optional') {
            if (method_exists('isVerificationRequired', $this)) {
                return $this->isVerificationRequired($action);
            } elseif ($this->$action === true) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

}
