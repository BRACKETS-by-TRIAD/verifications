<?php

namespace Brackets\Verifications\Traits;

use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

trait VerifiableTrait
{
    /** @var Collection */
    protected $activeVerifications;

    protected function loadActiveVerifications(string $action)
    {
        $newVerifications = $this->getNewVerifications($action);

        $this->activeVerifications = is_null($this->activeVerifications)
            ? $newVerifications
            : $this->activeVerifications->merge($newVerifications);
    }

    private function getNewVerifications(string $action): Collection
    {
        $now = Carbon::now();

        switch (Config::get('verifications.actions.'. $action .'.expires_from')) {
            case 'last-activity':
                 $verificationsQuery = VerificationCode::allActiveForAction($this, $action, $now->toDateTime());      //TODO: refactor to one query if possible?
                 $verificationsQuery->update([
                     'last_touched_at' => $now->toDateTime(),
                     'verifies_until' => $now->addMinutes(Config::get('verifications.actions.'. $action .'.expires_in'))->toDateTime()
                 ]);

                 $verifications = $verificationsQuery->get();

                 if (count($verifications) == 0) {
                     $this->activeVerifications = collect([]);
                 }

                 return $verifications;

            case 'verification':
                return VerificationCode::allFor($this)->where('verifies_until', '>=', $now->toDateTime())->get();

            default:
                throw new \Exception('Unspecified expiration type');
        }
    }

    public function isVerificationActive(string $action): bool
    {
        $this->loadActiveVerifications($action);

        return $this->activeVerifications->where('action_name', $action)->count() > 0;
    }

    public function isVerificationEnabled(string $action): bool                     // TODO: to be removed/refactored?
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
