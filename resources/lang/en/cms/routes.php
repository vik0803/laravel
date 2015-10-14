<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Routes
    |--------------------------------------------------------------------------
    |
    | Routes array
    |
    */

    '/' => '',
    'signout' => 'signout',
    \Config::get('app.defaultAuthRoute') => 'dashboard',
    'pages' => 'pages',
    'pf' => 'pf',
    'reset' => 'reset',
    'register' => 'register',
    'users' => 'users',
        'users/admins' => 'admins',
            'users/admins/create' => 'create',
        'users/operators' => 'operators',
    'profile' => 'profile',
    'messages' => 'messages',
    'settings' => 'settings',

];
