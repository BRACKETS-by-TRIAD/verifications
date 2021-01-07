<?php


namespace Brackets\Verifications\Channels\Contracts;

use Brackets\Verifications\Models\Verifiable;

interface ChannelProviderInterface
{
    public function sendCode(Verifiable $verifiable, string $code): void;
}
