<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth
    |--------------------------------------------------------------------------
    |
    | This is the Auth vars.
    |
    */

    'driver' => env('WABOXAPP_DRIVER', 'api'),
    'token' => env('WABOXAPP_TOKEN', ''),
    'url' => env('WABOXAPP_URL', 'www.waboxapp.com/api'),
    'uid' => env('WABOXAPP_UID', ''),

];
