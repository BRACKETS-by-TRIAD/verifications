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
                'verifiable_id' => $verifiable->getModelInstance()->id,
                'verifiable_type' => get_class($verifiable->getModelInstance()),
                'expires_at' => Carbon::now()->addMinutes(config('verifications.code.validity_length_minutes'))->toDateTime(),
                'used_at'
            ]);
        });
    }

    public function verifyCode(Verifiable $verifiable, $code)
    {
        $verificationCode = VerificationCode::where('verifiable_id', $verifiable->getModelInstance()->id)
                                            ->where('verifiable_type', get_class($verifiable->getModelInstance()))
                                            ->where('code', $code)
                                            ->whereNull('used_at')
                                            ->where('expire_at', '>', Carbon::now()->toDateTime())
                                            ->last();

        return $verificationCode ? true : false;
    }
}
