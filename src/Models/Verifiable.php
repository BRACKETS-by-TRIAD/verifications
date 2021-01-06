<?php

namespace Brackets\Verifications\Models;


interface Verifiable
{
    public function getPhoneAttribute(): string;

    public function getEmailAttribute(): string;

    public function isVerificationEnabled($action): bool;       // use for optional config setup
}
