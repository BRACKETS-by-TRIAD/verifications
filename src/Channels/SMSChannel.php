<?php

namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Models\Verifiable;
use Twilio\Rest\Client;

abstract class SMSChannel
{
    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Twilio\Exceptions\TwilioException
     */
    public static function sendSmsCode(Verifiable $verifiable, string $code): void
    {
        $twilioClient = new Client();

        try {
            $twilioClient->messages->create($verifiable->getPhoneAttribute(), [
                'from' => config('verifications.sms_from'),
                'body' => $code
            ]);

        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}
