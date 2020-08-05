<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Client ID
    |--------------------------------------------------------------------------
    |
    | The passport password grant client id to use for requesting tokens
    |
    */
    'client_id' => env('PASSPORT_PASSWORD_GRANT_CLIENT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Client secret
    |--------------------------------------------------------------------------
    |
    | The passport client secret to use for requesting tokens, this should
    | support the password grant
    |
    */
    'client_secret' => env('PASSPORT_PASSWORD_GRANT_CLIENT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Settings for email verification
    |--------------------------------------------------------------------------
    |
    | Update this values for your use case
    |
    */
    'verify_email' => [
        'base_url' => env('FRONT_URL').'/verify-email',
    ],
];