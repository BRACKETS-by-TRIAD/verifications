<?php

return [
    '2fa' => [
        'required_for_all_users' => true,
        'set_per_user_available' => false,
        'attributes' => [
            [
                'label' => 'Phone',
                'name' => 'phone'
            ]
        ]
    ],
    'code' => [
        'type' => 'numeric',                // specifies type of verification code, it has to be set to 'numeric' or 'string'
        'length' => 6,                      // specifies verification code length, set to 6 by default
        'validity_length_minutes' => 10     // specifies length of code validity
    ],
];
