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
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");

        $twilioClient = new Client($account_sid, $auth_token);

        try {
            $twilioClient->messages->create($verifiable->getPhoneAttribute(), [
                'from' => $twilio_number,
                'body' => $code
            ]);

        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}
