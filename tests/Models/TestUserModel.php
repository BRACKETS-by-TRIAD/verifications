<?php

namespace Models;

use Brackets\Verifications\Models\Verifiable;
use Brackets\Verifications\Traits\VerifiableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class TestUserModel extends User implements Verifiable
{
    use HasFactory,
        Notifiable,
        SoftDeletes,
        VerifiableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'deactivated_at',
        'consent_given_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPhoneAttribute(): ?string
    {
        return '0908 123 123';
    }
}