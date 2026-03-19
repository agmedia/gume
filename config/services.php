<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    'recaptcha' => [
        'sitekey'    => env('GOOGLE_RECAPTCHA_SITE_KEY'),
        'secret'     => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
    ],

    'mailchimp' => [
        'api_key'       => env('MAILCHIMP_API_KEY'),
        'audience_id'   => env('MAILCHIMP_AUDIENCE_ID'),
        'server_prefix' => env('MAILCHIMP_SERVER_PREFIX'),
    ],

    'intercars' => [
        'token_url'                => env('INTERCARS_TOKEN_URL', 'https://is.webapi.intercars.eu/oauth2/token'),
        'base_url'                 => env('INTERCARS_BASE_URL', 'https://api.webapi.intercars.eu'),
        'client_id'                => env('INTERCARS_CLIENT_ID'),
        'client_secret'            => env('INTERCARS_CLIENT_SECRET'),
        'scope'                    => env('INTERCARS_SCOPE', 'allinone'),
        'language'                 => env('INTERCARS_LANGUAGE', 'hr'),
        'timeout'                  => (int) env('INTERCARS_TIMEOUT', 120),
        'product_information_path' => env('INTERCARS_PRODUCT_INFORMATION_PATH', storage_path('app/intercars')),
    ],

    /*******************************************************************************
     *                              END Copyright : AGmedia                         *
     *******************************************************************************/

];
