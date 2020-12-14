<?php


namespace Brackets\Verifications\Models;


use Brackets\AdminGenerator\Generate\Model;

class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'code',
        'verifiable_id',
        'expires_at',
        'used_at'
    ];

}
