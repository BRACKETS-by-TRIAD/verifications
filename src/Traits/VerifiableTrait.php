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
                return $this->touchAndGetVerifications($now, $action);

            case 'verification':
                return VerificationCode::allFor($this)->where('verifies_until', '>=', $now->toDateTime())->get();

            default:
                throw new \Exception('Unspecified expiration type');
        }
    }

    private function touchAndGetVerifications(\DateTime $now, string $action): Collection
    {
        $verificationsQuery = VerificationCode::query()->allActiveForAction($this, $action, $now->toDateTime());
        $verificationsQuery->update([
            'last_touched_at' => $now->toDateTime(),
            'verifies_until' => $now->addMinutes(Config::get('verifications.actions.'. $action .'.expires_in'))->toDateTime()
        ]);

        return $verificationsQuery->get();
    }

    public function isVerificationActive(string $action): bool
    {
        $this->loadActiveVerifications($action);

        return $this->activeVerifications->where('action_name', $action)->count() > 0;
    }

    public function isVerificationEnabled(string $action): bool
    {
        if (Config::get('verifications.actions.'.$action.'.enabled')) {
            return method_exists($this, 'isVerificationRequired')
                ? $this->isVerificationRequired($action)
                : true;
        }

        return false;
    }
}
