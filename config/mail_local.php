<?php

return [
    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
            'stream' => [
                'ssl' => [
                    'verify_peer' => env('MAIL_VERIFY_PEER', true),
                    'verify_peer_name' => env('MAIL_VERIFY_PEER_NAME', true),
                    'allow_self_signed' => env('MAIL_ALLOW_SELF_SIGNED', false),
                ],
            ],
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@university-voting.local'),
        'name' => env('MAIL_FROM_NAME', 'University Voting Portal'),
    ],
];
