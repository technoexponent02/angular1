<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('CALLBACK_URL'),
    ],

    'twitter' => [
        'client_id'                 => env('TWITTER_CONSUMER_KEY'),
        'client_secret'             => env('TWITTER_CONSUMER_SECRET'),
        'redirect'                  => env('TWITTER_CALLBACK_URL'),
        'consumer_key'              => env('TWITTER_CONSUMER_KEY'),
        'consumer_key_secret'       => env('TWITTER_CONSUMER_SECRET'),
        'access_token'              => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret'       => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'login_url'                 => env('TWITTER_LOGIN_URL'),
        'callback_url'              => env('TWITTER_RETURN_URL'),
		'post_twitter_callback_url' => env('POST_TWITTER_CALLBACK_URL')
    ],

    'linkedin' => [
        'client_id'     => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect'      => env('LINKEDIN_CALLBACK_URL'),
    ],

    'mercury' => [
        'base_uri' => env('MERCURY_BASE_URI'),
        'key' => env('MERCURY_API_KEY')
    ],

    'mailchimp' => [
        'listid' => env('MAILCHIMP_LIST_ID'),
        'apikey' => env('MAILCHIMP_API_KEY')
    ],

];
