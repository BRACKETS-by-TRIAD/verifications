<?php

namespace Brackets\Verifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \DateTime|null verifies_until
 * @property \DateTime used_at
 */
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

    public function scopeAllFor(Builder $query, Verifiable $verifiable)
    {
        return $query->where('verifiable_type', $verifiable->getMorphClass())
                     ->where('verifiable_id', $verifiable->getKey());
    }
}
