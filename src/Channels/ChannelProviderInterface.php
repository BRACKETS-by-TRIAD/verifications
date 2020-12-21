<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Models\Verifiable;

interface ChannelProviderInterface
{
    public function sendCode(Verifiable $verifiable, string $code): void;
}
