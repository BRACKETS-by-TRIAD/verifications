<?php

return [
    'enabled' => true, // true, false                      // global package enable/disable for test purposes @ localhost
    'actions' => [
        'download-invoice' => [
            'enabled' => 'forced',                         // forced, optional, false
            'channel' => 'sms',                            // sms, email
            'code' => [
                'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                      // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10     // specifies length of code validity
            ],
        ],
        'withdraw-money' => [
            'enabled' => 'optional',                   // forced, optional, false
            'channel' => 'sms',                        // sms, email
            'code' => [
                'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                      // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10     // specifies length of code validity
            ],
        ],
        '2fa_users' => [
            'enabled' => 'optional',                          // forced, optional, false
            'channel' => 'sms',                             // sms, email
            'code' => [
                'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
                'length' => 6,                      // specifies verification code length, set to 6 by default
                'validity_length_minutes' => 10     // specifies length of code validity
            ],
        ],
    ]
];
