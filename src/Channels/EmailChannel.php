<?php


namespace Brackets\Verifications\Channels;


use Brackets\Verifications\Models\Verifiable;

abstract class EmailChannel
{
    /**
     * @param Verifiable $verifiable
     * @param String $code
     * @throws \Exception
     */
    public static function sendEmailCode(Verifiable $verifiable, String $code): void
    {
        $recipient = $verifiable->getEmailAttribute();

        try {
            //TODO
        } catch(\Exception $ex) {
            throw $ex;
        }
    }
}