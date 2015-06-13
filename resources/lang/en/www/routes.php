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

    '/' => ['slug' => '', 'get' => 'LoginController@show', 'post' => 'LoginController@login'],
    'logout' => ['slug' => 'logout', 'get' => 'LoginController@logout'],
    'home' => ['slug' => 'home', 'get' => 'PageController@home'],
    'page' => ['slug' => 'page', 'get' => 'PageController@show'],
    'pf' => ['slug' => 'pf', 'get' => 'PasswordController@show', 'post' => 'PasswordController@pf', '/' => [
        'mail' => ['slug' => 'mail', 'get' => 'PageController@showSubpage'],
        'reset' => ['slug' => 'reset', 'get' => 'PageController@getReset'],
        ]
    ],
    'reset' => ['slug' => 'reset', 'get' => 'PasswordController@getReset', 'post' => 'PasswordController@postReset'],

];
