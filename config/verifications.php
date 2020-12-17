<?php

return [
    'enabled' => true, // true, false                      // global package enable/disable for test purposes @ localhost
    'actions' => [
        'invoices' => [
            'enabled' => true,
            'model' => \App\Core\Models\Invoice::class,   // implements Verifiable
            'channel' => 'sms'
        ]
    ],
    '2fa' => [
        'admin-users' => [
            'enabled' => 'forced',                          // forced, optional, false
            'model' => \App\Core\Models\AdminUser::class,   // implements Verifiable
            'channel' => 'sms',                             // sms, email
        ],
        'customers' => [
            'enabled' => 'optional',                        // forced, optional, false
            'model' => \App\Core\Models\User::class,        // implements Verifiable
            'channel' => 'sms',                             // sms, email
        ],
    ],

    'code' => [
        'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
        'length' => 6,                      // specifies verification code length, set to 6 by default
        'validity_length_minutes' => 10     // specifies length of code validity
    ],
];
