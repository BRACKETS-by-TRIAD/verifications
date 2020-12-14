<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Models\Verifiable;

abstract class EmailChannel
{
    /**
     * @param Verifiable $verifiable
     * @param string $code
     * @throws \Exception
     */
    public static function sendEmailCode(Verifiable $verifiable, string $code): void
    {
        $recipient = $verifiable->getEmailAttribute();

        try {
            //TODO
        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}
