<?php


namespace Brackets\Verifications\Repositories;


use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class VerificationCodesRepository
{
    public function createCode(Verifiable $verifiable, string $action, string $code)
    {
        return DB::transaction(function () use ($verifiable, $action, $code) {
            $codeValidInMinutes = Config::get('verifications.actions.'. $action .'.code.validity_length_minutes');

            return VerificationCode::create([
                'verifiable_id' => $verifiable->getKey(),
                'verifiable_type' => get_class($verifiable),
                'code' => $code,
                'action_name' => $action,
                'expires_at' => Carbon::now()->addMinutes($codeValidInMinutes)->toDateTime()
            ]);
        });
    }

    public function verifyCode(Verifiable $verifiable, string $action, string $code): bool
    {
        $now = Carbon::now()->toDateTime();

        $verificationCode = VerificationCode::where('verifiable_id', $verifiable->getKey())
                                            ->where('verifiable_type', get_class($verifiable))
                                            ->where('action_name', $action)
                                            ->where('code', $code)
                                            ->whereNull('used_at')
                                            ->where('expire_at', '>', $now)
                                            ->last();   // used last, if for some reason user refreshes the page and generate a new code for the same action

        if($verificationCode) {
            $actionVerifiedMinutes = Config::get('verifications.actions.'. $action .'.verified_action_valid_minutes');

            $verificationCode->used_at = $now;
            $verificationCode->verifies_until = Carbon::parse($now)->addMinutes($actionVerifiedMinutes)->toDateTime();

            $verificationCode->save();

            return true;
        }

        return false;
    }
}
