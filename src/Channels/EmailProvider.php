<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Models\Verifiable;

class EmailProvider implements EmailProviderInterface
{
    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Exception
     */
    public function sendCode(Verifiable $verifiable, string $code): void
    {
        $recipient = $verifiable->getEmailAttribute();

        try {
            //TODO
        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}
