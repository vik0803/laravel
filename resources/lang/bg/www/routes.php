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
    'logout' => ['slug' => 'изход', 'get' => 'AuthController@getLogout'],
    'home' => ['slug' => 'начало', 'get' => 'PageController@home'],
    'page' => ['slug' => 'страница', 'get' => 'PageController@page'],
    'pf' => ['slug' => 'забравена-парола', 'get' => 'PasswordController@getEmail', 'post' => 'PasswordController@postEmail'],
    'reset' => ['slug' => 'възстанови-парола', 'get' => ['controller' => 'PasswordController@getReset', 'parameters' => '{token}'], 'post' => 'PasswordController@postReset'],
    'register' => ['slug' => 'регистрация', 'get' => 'AuthController@getRegister', 'post' => 'AuthController@postRegister'],

];
