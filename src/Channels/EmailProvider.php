<?php


namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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
            Mail::send("brackets/verifications::email.verification-email", ['code' => $code], function ($message) use ($recipient) {
                // TODO how to provide subject customization?
                $message->subject('Verification code' .' | '. Config::get('app.name'));
                $message->to($recipient);
            });
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
