<?php

namespace Brackets\Verifications\Models;


use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'verifiable_id',
        'verifiable_type',
        'code',
        'action_name',
        'expires_at',
        'verifies_until',
        'used_at'
    ];
}