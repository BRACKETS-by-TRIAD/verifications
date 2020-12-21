<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Models\Verifiable;
use Twilio\Rest\Client;

class TwilioProvider implements SMSProviderInterface
{
    /**
     * @var Client
     */
    private $twilioClient;

    private $twilio_number;

    public function __construct()
    {
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $this->twilio_number = getenv("TWILIO_NUMBER");

        $this->twilioClient = new Client($account_sid, $auth_token);
    }

    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function sendCode(Verifiable $verifiable, string $code): void
    {
        try {
            $this->twilioClient->messages->create($verifiable->getPhoneAttribute(), [
                'from' => $this->twilio_number,
                'body' => $code
            ]);

        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}
