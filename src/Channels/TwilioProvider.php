<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Channels\Contracts\SMSProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Config;
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
        $account_sid = Config::get('twilio.account_sid');
        $auth_token = Config::get('twilio.auth_token');
        $this->twilio_number = Config::get('twilio.twilio_number');

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
