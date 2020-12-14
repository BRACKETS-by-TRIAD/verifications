<?php

namespace Brackets\Verifications\Models;

use Illuminate\Database\Eloquent\Model;

interface Verifiable
{

    public function getModelInstance(): Model;

    public function getPhoneAttribute(): string;

    public function getEmailAttribute(): string;
}
