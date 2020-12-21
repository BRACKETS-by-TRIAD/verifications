<?php


namespace Brackets\Verifications\Repositories;


use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerificationCodesRepository
{
    public function createCode(Verifiable $verifiable, $code)
    {
        return DB::transaction(function () use ($verifiable, $code) {
            return VerificationCode::create([
                'code' => $code,
                'verifiable_id' => $verifiable->getKey(),
                'verifiable_type' => get_class($verifiable),
                'expires_at' => Carbon::now()->addMinutes(config('verifications.code.validity_length_minutes'))->toDateTime()
            ]);
        });
    }

    public function verifyCode(Verifiable $verifiable, $code): bool
    {
        $now = Carbon::now()->toDateTime();

        $verificationCode = VerificationCode::where('verifiable_id', $verifiable->getKey())
                                            ->where('verifiable_type', get_class($verifiable))
                                            ->where('code', $code)
                                            ->whereNull('used_at')
                                            ->where('expire_at', '>', $now)
                                            ->last();

        if($verificationCode) {
            $verificationCode->used_at = $now;

            return true;
        }

        return false;
    }
}
