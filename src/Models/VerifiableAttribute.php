<?php


namespace Brackets\Verifications\Models;


use Illuminate\Database\Eloquent\Model;

class VerifiableAttribute extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'code',
        'verifiable_id',
        'expires_at',
        'used_at'
    ];
}
