<?php

return [
	/*
    |--------------------------------------------------------------------------
    | Customization settings depending upon server
    |--------------------------------------------------------------------------    |
    */

    'google' => [
        'analytics' => env('GOOGLE_ANALYTICS'),
        'tag_manager' => env('GOOGLE_TAG_MANAGER'),
    ],
];