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

    '/' => ['id' => 1, 'slug' => '', 'get' => 'AuthController@getLogin', 'post' => 'AuthController@postLogin'],
        'logout' => ['id' => 2, 'slug' => 'logout', 'get' => 'AuthController@getLogout'],
        'dashboard' => ['id' => 3, 'slug' => 'dashboard', 'get' => 'PageController@dashboard'],
        'pages' => ['id' => 4, 'slug' => 'pages', 'get' => 'PageController@pages'],
        'pf' => ['id' => 5, 'slug' => 'pf', 'get' => 'PasswordController@getEmail', 'post' => 'PasswordController@postEmail'],
        'reset' => ['id' => 6, 'slug' => 'reset', 'get' => ['controller' => 'PasswordController@getReset', 'parameters' => '{token}'], 'post' => 'PasswordController@postReset'],
        'register' => ['id' => 7, 'slug' => 'register', 'get' => 'AuthController@getRegister', 'post' => 'AuthController@postRegister'],
        'users' => ['id' => 8, 'slug' => 'users', 'get' => 'UserController@users', '/' => [
            'admins' => ['id' => 1, 'slug' => 'admins', 'get' => 'UserController@admins'],
            'operators' => ['id' => 2, 'slug' => 'operators', 'get' => 'UserController@operators'],
            ]
        ],
        'profile' => ['id' => 3, 'slug' => 'profile', 'get' => 'PageController@dashboard'],
        'messages' => ['id' => 3, 'slug' => 'messages', 'get' => 'PageController@dashboard'],
        'settings' => ['id' => 3, 'slug' => 'settings', 'get' => 'PageController@dashboard'],

];
