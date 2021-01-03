<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
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
        $account_sid = config('twilio.account_sid');
        $auth_token = config('twilio.auth_token');
        $this->twilio_number = config('twilio.twilio_number');

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
