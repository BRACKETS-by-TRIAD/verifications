<?php

namespace Brackets\Verifications\Models;

interface Verifiable
{

    public function getModel(): self;

    public function getPhoneAttribute();

    public function getEmailAttribute();
}
