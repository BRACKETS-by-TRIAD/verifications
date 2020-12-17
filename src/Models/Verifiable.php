<?php

namespace Brackets\Verifications\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Verifiable
{
    public function getPhoneAttribute(): string;

    public function getEmailAttribute(): string;
}
