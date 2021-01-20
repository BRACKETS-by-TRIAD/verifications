<?php

namespace Brackets\Verifications\Channels;

use Brackets\Verifications\Channels\Contracts\EmailProviderInterface;
use Illuminate\Support\Facades\View;

abstract class SMSProvider implements EmailProviderInterface
{
    public function renderSMSMessage($code): string
    {
        return View::make("brackets/verifications::sms.verification-sms", ['code' => $code])->render();
    }
}
