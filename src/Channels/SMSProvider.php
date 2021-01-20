<?php


namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Brackets\Verifications\Models\Verifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

abstract class SMSProvider implements EmailProviderInterface
{
    public function renderSMSMessage($code)
    {
        return View::make("brackets/verifications::sms.verification-sms", ['code' => $code])->render();
    }
}
