<?php

return [
    'account_sid' => getenv("TWILIO_SID"),
    'auth_token' => getenv("TWILIO_AUTH_TOKEN"),
    'twilio_number' => getenv("TWILIO_NUMBER")
];
