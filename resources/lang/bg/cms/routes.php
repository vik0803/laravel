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
    'logout' => ['id' => 2, 'slug' => 'изход', 'get' => 'AuthController@getLogout'],
    'home' => ['id' => 3, 'slug' => 'начало', 'get' => 'PageController@home'],
    'page' => ['id' => 4, 'slug' => 'страница', 'get' => 'PageController@page'],
    'pf' => ['id' => 5, 'slug' => 'забравена-парола', 'get' => 'PasswordController@getEmail', 'post' => 'PasswordController@postEmail'],
    'reset' => ['id' => 6, 'slug' => 'възстанови-парола', 'get' => ['controller' => 'PasswordController@getReset', 'parameters' => '{token}'], 'post' => 'PasswordController@postReset'],
    'register' => ['id' => 7, 'slug' => 'регистрация', 'get' => 'AuthController@getRegister', 'post' => 'AuthController@postRegister'],

];
