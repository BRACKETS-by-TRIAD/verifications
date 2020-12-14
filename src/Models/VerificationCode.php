<?php


namespace Brackets\Verifications\Models;


use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'code',
        'verifiable_id',
        'verifiable_type',
        'expires_at',
        'used_at'
    ];
}
