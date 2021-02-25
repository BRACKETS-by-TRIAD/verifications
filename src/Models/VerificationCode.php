<?php

namespace Brackets\Verifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * @property \DateTime|null verifies_until
 * @property \DateTime used_at
 * @property \DateTime last_touched_at
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
        'used_at',
        'last_touched_at',
        'ip_address',
        'user_agent'
    ];

    public function scopeAllFor(Builder $query, Verifiable $verifiable)
    {
        return $query->where('verifiable_type', $verifiable->getMorphClass())
                     ->where('verifiable_id', $verifiable->getKey())
                     ->where('ip_address', Request::ip())
                     ->where('user_agent', Request::userAgent());
    }

    public function scopeAllActiveForAction(Builder $query, Verifiable $verifiable, string $action, \DateTime $dateTime)
    {
        return $query->allFor($verifiable)
                     ->where('action_name', $action)
                     ->where('verifies_until', '>=', $dateTime);
    }
}
