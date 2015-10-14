<?php

/*
|--------------------------------------------------------------------------
| Dynamic Application Routes
|--------------------------------------------------------------------------
*/

$subdomain = explode('.', strtolower(\Request::getHost()))[0];

if ($subdomain == 'cms') {
    Route::group(['domain' => $subdomain . '.' . config('app.domain'), 'namespace' => ucfirst($subdomain)], function() {

        foreach (array_keys(\Locales::getSupportedLocales()) as $locale) {
            \Locales::setRoutesLocale($locale);

            Route::group(['middleware' => 'guest'], function() {
                Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name(\Locales::getRoutesLocalePrefix() . '/');
                Route::post(\Locales::getRoute('/'), 'AuthController@postLogin');

                Route::get(\Locales::getRoute('pf'), 'PasswordController@getEmail')->name(\Locales::getRoutesLocalePrefix() . 'pf');
                Route::post(\Locales::getRoute('pf'), 'PasswordController@postEmail');

                Route::get(\Locales::getRoute('reset') . '/{token}', 'PasswordController@getReset')->name(\Locales::getRoutesLocalePrefix() . 'reset');
                Route::post(\Locales::getRoute('reset'), 'PasswordController@postReset');
            });

            Route::group(['middleware' => 'auth'], function() {
                Route::get(\Locales::getRoute('register'), 'AuthController@getRegister')->name(\Locales::getRoutesLocalePrefix() . 'register');
                Route::post(\Locales::getRoute('register'), 'AuthController@postRegister');

                Route::get(\Locales::getRoute('signout'), 'AuthController@getSignout')->name(\Locales::getRoutesLocalePrefix() . 'signout');

                Route::get(\Locales::getRoute(\Config::get('app.defaultAuthRoute')), 'PageController@' . \Config::get('app.defaultAuthRoute'))->name(\Locales::getRoutesLocalePrefix() . \Config::get('app.defaultAuthRoute'));

                Route::get(\Locales::getRoute('pages'), 'PageController@pages')->name(\Locales::getRoutesLocalePrefix() . 'pages');

                Route::get(\Locales::getRoute('users') . '/{group?}', 'UserController@index')->name(\Locales::getRoutesLocalePrefix() . 'users')->where('group', 'admins|operators');
                Route::get(\Locales::getRoute('users') . '/create', 'UserController@index')->name(\Locales::getRoutesLocalePrefix() . 'users/create');
                /*Route::group(['as' => \Locales::getRoutesLocalePrefix() . 'users/', 'prefix' => \Locales::getRoute('users')], function() {
                    Route::get(\Locales::getSubRoute('users/admins'), 'UserController@index')->name('admins');
                    Route::group(['as' => 'admins/', 'prefix' => \Locales::getSubRoute('users/admins')], function() {
                        Route::get(\Locales::getSubRoute('users/admins/create'), 'UserController@create')->name('create');
                        Route::post(\Locales::getSubRoute('users/admins/create'), 'UserController@store');
                    });

                    Route::get(\Locales::getSubRoute('users/operators'), 'UserController@index')->name('operators');
                });*/

                Route::get(\Locales::getRoute('profile'), 'PageController@pages')->name(\Locales::getRoutesLocalePrefix() . 'profile');

                Route::get(\Locales::getRoute('messages'), 'PageController@pages')->name(\Locales::getRoutesLocalePrefix() . 'messages');

                Route::get(\Locales::getRoute('settings'), 'PageController@pages')->name(\Locales::getRoutesLocalePrefix() . 'settings');
            });
        }

    });
} elseif ($subdomain == 'www') {
    Route::group(['domain' => $subdomain . '.' . config('app.domain'), 'namespace' => ucfirst($subdomain)], function() {
        Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name('/');
    });
}
