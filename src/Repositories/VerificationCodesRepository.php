<?php

namespace Brackets\Verifications\Repositories;

use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class VerificationCodesRepository
{
    public function createCode(Verifiable $verifiable, string $action, string $ipAddress, string $userAgent, string $code)
    {
        return DB::transaction(function () use ($verifiable, $action, $ipAddress, $userAgent, $code) {
            $codeValidInMinutes = Config::get('verifications.actions.'. $action .'.code.expires_in');

            return VerificationCode::create([
                'verifiable_id' => $verifiable->getKey(),
                'verifiable_type' => $verifiable->getMorphClass(),
                'code' => $code,
                'ip_address' => $ipAddress,
                'action_name' => $action,
                'user_agent' => $userAgent,
                'expires_at' => Carbon::now()->addMinutes($codeValidInMinutes)->toDateTime()
            ]);
        });
    }

    public function verifyCode(Verifiable $verifiable, string $action, string $code): bool
    {
        $now = Carbon::now()->toDateTime();

        $verificationCode = VerificationCode::allFor($verifiable)
                                            ->where('action_name', $action)
                                            ->where('code', $code)
                                            ->whereNull('used_at')
                                            ->where('expires_at', '>', $now)
                                            ->orderBy('created_at', 'desc') // use last record, if for some reason user refreshes the page and generate a new code for the same action
                                            ->first();

        return $verificationCode ? $this->updateVerifiedCode($verificationCode, $action, $now) : false;
    }

    private function updateVerifiedCode(VerificationCode $verificationCode, string $action, \DateTime $now): bool
    {
        $actionVerifiedMinutes = Config::get('verifications.actions.'. $action .'.expires_in');

        $verificationCode->verifies_until = Carbon::parse($now)->addMinutes($actionVerifiedMinutes)->toDateTime();
        $verificationCode->used_at = $now;
        $verificationCode->last_touched_at = Config::get('verifications.actions.'. $action .'.expires_from') === 'last-activity' ? $now : null;

        $verificationCode->save();

        return true;
    }
}
