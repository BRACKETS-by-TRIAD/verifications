<?php

namespace Brackets\Verifications\Models;

interface Verifiable
{
    public function getPhoneAttribute(): string;

    public function getEmailAttribute(): string;

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass();
}
