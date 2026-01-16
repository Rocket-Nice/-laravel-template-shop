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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'delivery' => [
        'cdek' => 'СДЭК до ПВЗ',
        'cdek_courier' => 'СДЭК Курьер',
        'boxberry' => 'Boxberry Доставка',
        // 'bxb' => 'Международная доставка',
        'pochta' => 'Доставка Почтой'
    ],
    'boxberry' => [
        'apikey' => env('BOXBERRY_API_KEY', null)
    ],

    'robokassa' => [
        'login' => env('ROBOKASSA_LOGIN', null),
        'pass1' => env('ROBOKASSA_KEY1', null),
        'pass2' => env('ROBOKASSA_KEY2', null),
        'test1' => env('ROBOKASSA_KEY1_TEST', null),
        'test2' => env('ROBOKASSA_KEY2_TEST', null),

    ],

    'dashamail' => [
        'username' => env('DASHAMAIL_USERNAME'),
        'password' => env('DASHAMAIL_PASSWORD'),
        'list_id_main' => env('DASHAMAIL_LIST_ID_MAIN'),
        'list_id_secondary' => env('DASHAMAIL_LIST_ID_SECONDARY'),
    ],
];
