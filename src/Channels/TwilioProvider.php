<?php


namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Config;
use Twilio\Rest\Client;

class TwilioProvider extends SMSProvider
{
    /** @var Client */
    private $twilioClient;

    private $twilio_number;

    public function __construct()
    {
        $account_sid = Config::get('twilio.account_sid');
        $auth_token = Config::get('twilio.auth_token');
        $this->twilio_number = Config::get('twilio.twilio_number');

        $this->twilioClient = new Client($account_sid, $auth_token);      // commented in case of test purposes
    }

    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Exception
     */
    public function sendCode(Verifiable $verifiable, string $code): void
    {
        try {
            $this->twilioClient->messages->create($verifiable->getPhoneAttribute(), [
                'from' => $this->twilio_number,
                'body' => $this->renderSMSMessage($code)
            ]);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
