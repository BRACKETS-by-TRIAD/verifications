<?php

namespace Brackets\Verifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;

interface Verifiable
{
    /**
     * Should return $this
     *
     * @return Model
     */
    public function getModelInstance(): Model;

    public function verifiableAttributes(): MorphMany;

    public function getPhoneAttribute(): string;

    public function getEmailAttribute(): string;

    /**
     * Should return $this->verifiableAttributes()->fill($request)->save();
     *
     * @param Request $request
     * @return Model
     */
    public function saveVerifiableAttributes(Request $request): Model;
}
