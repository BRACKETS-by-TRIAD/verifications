<?php


namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Log;

class LogSMSProvider extends SMSProvider
{
    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Exception
     */
    public function sendCode(Verifiable $verifiable, string $code): void
    {
        Log::info("New SMS verification to: " . $verifiable->getPhoneAttribute() . " with message: " . $this->renderSMSMessage($code));
    }
}
