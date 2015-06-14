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

    '/' => ['slug' => '', 'get' => 'AuthController@getLogin', 'post' => 'AuthController@postLogin'],
    'logout' => ['slug' => 'logout', 'get' => 'AuthController@getLogout'],
    'home' => ['slug' => 'home', 'get' => 'PageController@home'],
    'page' => ['slug' => 'page', 'get' => 'PageController@page'],
    'pf' => ['slug' => 'pf', 'get' => 'PasswordController@getEmail', 'post' => 'PasswordController@postEmail'],
    'reset' => ['slug' => 'reset', 'get' => ['controller' => 'PasswordController@getReset', 'parameters' => '{token}'], 'post' => 'PasswordController@postReset'],
    'register' => ['slug' => 'register', 'get' => 'AuthController@getRegister', 'post' => 'AuthController@postRegister'],

];
