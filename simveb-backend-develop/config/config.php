<?php
return [
    'invitation_duration' => env('INVITATION_DURATION', 24),
    'app_url' => env('APP_URL', "https://nautilus-technology.net"),
    'fedapay_pk' => env('FEDAPAY_PK', "pk_sandbox_mmWicFggMFQDW8qdrGmRBV9A"),
    'fedapay_sk' => env('FEDAPAY_SK', "sk_sandbox_y7plFA3lQHh7zfHEYeD8ImPR"),
    'fedapay_env' => env('FEDAPAY_ENV', "sandbox"),

    'kkiapay_pk' => env('KKIAPAY_PK', "5766c4e0824211efb2cd736c2a0bab43"),
    'kkiapay_sk' => env('KKIAPAY_SK', "tpk_5766ebf1824211efb2cd736c2a0bab43"),
    'kkiapay_sand' => env('KKIAPAY_ENV', "sandbox"),
    'kkiapay_sec' => env('KKIAPAY_SEC', ''),

    'backoffice_url' => env('BACKOFFICE_URL', 'http://localhost:3000'),
    'landingpage_url' => env('LANDINGPAGE_URL', 'http://localhost:8001'),
    'middleware_url' => env('MIDDLEWARE_URL', 'http://localhost:8003'),
    'institution_registration_duration' => env('INSTITUTION_REGISTRATION_DURATION', 24),

    'check_npi_url' => env('CHECK_NPI_URL', 'https://sandbox-api.simveb-bj.com/api/persons'),
    'check_ifu_url' => env('CHECK_IFU_URL', 'https://sandbox-api.simveb-bj.com/api/companies'),

    'xroad_base_url' => env('XROAD_BASE_URL'),
    'dgi_token' => env('DGI_TOKEN'),

    'sms' => [
        'driver' => env('SMS_DRIVER', 'wirepick'),
    ],

    'wirepick' => [
        'host' => env('WIREPICK_HOST', 'https://apisms.wirepick.com/'),
        'user' => env('WIREPICK_USER', ''),
        'password' => env('WIREPICK_PASSWORD', ''),
        'senderId' => env('WIREPICK_SENDER_ID', 'MAEP')
    ],
];
