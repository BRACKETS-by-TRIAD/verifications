<?php

return [
    'enabled' => env('VERIFICATION_ENABLED', true), // you can enable/disable globally (i.e. disabled for tests/dev env)
    'actions' => [
//        'my-action' => [
//            'enabled' => true,                              // you can enable/disable single action
//            'channel' => 'sms',                             // currently: sms, email
//            'keep_verified_during_session' => false,        // if true, keeps verification valid while session exists
//            'verified_action_valid_minutes' => 15,          // if keep_verified_during_session == false, then this config specifies how many minutes does it take to require another code verification for the same action
//            'code' => [
//                'type' => 'numeric',                        // specifies the type of verification code, can be one of: 'numeric' or 'string'
//                'length' => 6,                              // specifies the verification code length, defaults to 6
//                'validity_length_minutes' => 10,            // specifies the length in minutes how long the code will be valid for use
//            ],
//        ],
//        '2FA' => [
//            'enabled' => true,                              // you can enable/disable single action
//            'channel' => 'sms',                             // currently: sms, email
//            'keep_verified_during_session' => true,         // if true, keeps verification valid while session exists
//            'code' => [
//                'type' => 'numeric',                        // specifies the type of verification code, can be one of: 'numeric' or 'string'
//                'length' => 6,                              // specifies the verification code length, defaults to 6
//                'validity_length_minutes' => 10,            // specifies the length in minutes how long the code will be valid for use
//            ],
//        ],
    ]
];
