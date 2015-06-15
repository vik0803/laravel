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
    'home' => ['id' => 3, 'slug' => 'home', 'get' => 'PageController@home'],
    'page' => ['id' => 4, 'slug' => 'page', 'get' => 'PageController@page'],
    'pf' => ['id' => 5, 'slug' => 'pf', 'get' => 'PasswordController@getEmail', 'post' => 'PasswordController@postEmail'],
    'reset' => ['id' => 6, 'slug' => 'reset', 'get' => ['controller' => 'PasswordController@getReset', 'parameters' => '{token}'], 'post' => 'PasswordController@postReset'],
    'register' => ['id' => 7, 'slug' => 'register', 'get' => 'AuthController@getRegister', 'post' => 'AuthController@postRegister'],

];
