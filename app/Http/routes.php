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
                Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name(\Locales::getRouteName('/'));
                Route::post(\Locales::getRoute('/'), 'AuthController@postLogin');

                Route::get(\Locales::getRoute('pf'), 'PasswordController@getEmail')->name(\Locales::getRouteName('pf'));
                Route::post(\Locales::getRoute('pf'), 'PasswordController@postEmail');

                Route::get(\Locales::getRoute('reset') . '/{token}', 'PasswordController@getReset')->name(\Locales::getRouteName('reset'));
                Route::post(\Locales::getRoute('reset'), 'PasswordController@postReset');
            });

            Route::group(['middleware' => 'auth'], function() {
                \Locales::isRoute('register') ? Route::get(\Locales::getRoute('register'), 'AuthController@getRegister')->name(\Locales::getRouteName('register')) : '';
                Route::post(\Locales::getRoute('register'), 'AuthController@postRegister');

                Route::get(\Locales::getRoute('signout'), 'AuthController@getSignout')->name(\Locales::getRouteName('signout'));

                Route::get(\Locales::getRoute(\Config::get('app.defaultAuthRoute')), 'PageController@' . \Config::get('app.defaultAuthRoute'))->name(\Locales::getRouteName(\Config::get('app.defaultAuthRoute')));

                \Locales::isRoute('pages') ? Route::get(\Locales::getRoute('pages'), 'PageController@pages')->name(\Locales::getRouteName('pages')) : '';

                \Locales::isRoute('users') ? Route::get(\Locales::getRoute('users') . '/{group?}', 'UserController@index')->name(\Locales::getRouteName('users'))->where('group', \Locales::getRouteRegex('users')) : '';
                \Locales::isRoute('users/create') ? Route::get(\Locales::getRoute('users/create'), 'UserController@create')->name(\Locales::getRouteName('users/create')) : '';
                Route::post(\Locales::getRoute('users/create'), 'UserController@store');

                \Locales::isRoute('clients') ? Route::get(\Locales::getRoute('clients'), 'UserController@index')->name(\Locales::getRouteName('clients')) : '';
                \Locales::isRoute('clients/level1') ? Route::get(\Locales::getRoute('clients/level1'), 'UserController@index')->name(\Locales::getRouteName('clients/level1')) : '';
                \Locales::isRoute('clients/level2') ? Route::get(\Locales::getRoute('clients/level2'), 'UserController@index')->name(\Locales::getRouteName('clients/level2')) : '';

                \Locales::isRoute('profile') ? Route::get(\Locales::getRoute('profile'), 'PageController@pages')->name(\Locales::getRouteName('profile')) : '';

                \Locales::isRoute('messages') ? Route::get(\Locales::getRoute('messages'), 'PageController@pages')->name(\Locales::getRouteName('messages')) : '';

                \Locales::isRoute('settings') ? Route::get(\Locales::getRoute('settings'), 'PageController@pages')->name(\Locales::getRouteName('settings')) : '';
            });
        }

    });
} elseif ($subdomain == 'www') {
    Route::group(['domain' => $subdomain . '.' . config('app.domain'), 'namespace' => ucfirst($subdomain)], function() {
        Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name('/');
    });
}
