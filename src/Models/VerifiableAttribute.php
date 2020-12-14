<?php


namespace Brackets\Verifications\Models;


use Illuminate\Database\Eloquent\Model;

class VerifiableAttribute extends Model
{
    protected $table = 'verifiable_attributes';

    protected $fillable = [
        'code',
        'verifiable_id',
        'verifiable_type',
        'attribute_name',
        'attribute_value'
    ];
}
