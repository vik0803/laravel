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
    'logout' => ['slug' => 'изход', 'get' => 'LoginController@logout'],
    'home' => ['slug' => 'начало', 'get' => 'PageController@home'],
    'page' => ['slug' => 'страница', 'get' => 'PageController@show'],
    'pf' => ['slug' => 'забравена-парола', 'get' => 'PasswordController@show', 'post' => 'PasswordController@pf', '/' => [
        'mail' => ['slug' => 'изпрати', 'get' => 'PageController@showSubpage'],
        'reset' => ['slug' => 'възстанови', 'get' => 'PageController@getReset'],
        ]
    ],
    'reset' => ['slug' => 'възстанови-забравената-парола', 'get' => 'PasswordController@getReset', 'post' => 'PasswordController@postReset'],

];
