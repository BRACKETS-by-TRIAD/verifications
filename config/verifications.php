<?php

return [
    'enabled' => env('VERIFICATION_ENABLED', true),         // you can enable/disable globally (i.e. disabled for tests/dev env)
    'actions' => [
//        'my-action' => [
//            'enabled' => true,                                          // you can enable/disable single action
//            'channel' => 'sms',                                         // currently: sms, email
//            'form_template' => 'brackets/verifications::verification',  // blade name with namespace used for verification code form
//            'expires_in' => 15,                                         // specifies how many minutes does it take to require another code verification for the same action
//            'expires_from' => 'verification',                           // one of: 'last-activity' or 'verification', specifies what triggers the expiration (see expires_in)
//            'code' => [
//                'type' => 'numeric',                                    // specifies the type of verification code, can be one of: 'numeric' or 'string'
//                'length' => 6,                                          // specifies the verification code length, defaults to 6
//                'expires_in' => 10,                                     // specifies how many minutes is the code valid
//            ]
//        ],
//        '2FA' => [
//            'enabled' => true,                                          // you can enable/disable single action
//            'channel' => 'sms',                                         // currently: sms, email
//            'form_template' => 'brackets/verifications::verification',  // blade name with namespace used for verification code form
//            'expires_in' => 15,                                         // specifies how many minutes does it take to require another code verification for the same action
//            'expires_from' => 'last-activity',                          // one of: 'last-activity' or 'verification', specifies what triggers the expiration (see expires_in)
//            'code' => [
//                'type' => 'numeric',                                    // specifies the type of verification code, can be one of: 'numeric' or 'string'
//                'length' => 6,                                          // specifies the verification code length, defaults to 6
//                'expires_in' => 10,                                     // specifies how many minutes is the code valid
//            ]
//        ]
    ]
];
