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
                Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name(\Locales::getRoutePrefix('/'));
                Route::post(\Locales::getRoute('/'), 'AuthController@postLogin');

                Route::get(\Locales::getRoute('pf'), 'PasswordController@getEmail')->name(\Locales::getRoutePrefix('pf'));
                Route::post(\Locales::getRoute('pf'), 'PasswordController@postEmail');

                Route::get(\Locales::getRoute('reset') . '/{token}', 'PasswordController@getReset')->name(\Locales::getRoutePrefix('reset'));
                Route::post(\Locales::getRoute('reset'), 'PasswordController@postReset');
            });

            Route::group(['middleware' => 'auth'], function() {
                \Locales::isRoute('register') ? Route::get(\Locales::getRoute('register'), 'AuthController@getRegister')->name(\Locales::getRoutePrefix('register')) : '';
                Route::post(\Locales::getRoute('register'), 'AuthController@postRegister');

                Route::get(\Locales::getRoute('signout'), 'AuthController@getLogout')->name(\Locales::getRoutePrefix('signout'));

                Route::get(\Locales::getRoute(\Config::get('app.defaultAuthRoute')), 'PageController@' . \Config::get('app.defaultAuthRoute'))->name(\Locales::getRoutePrefix(\Config::get('app.defaultAuthRoute')));

                \Locales::isRoute('pages') ? Route::get(\Locales::getRoute('pages'), 'PageController@pages')->name(\Locales::getRoutePrefix('pages')) : '';

                \Locales::isRoute('users') ? Route::get(\Locales::getRoute('users') . '/{group?}', 'UserController@index')->name(\Locales::getRoutePrefix('users'))->where('group', \Locales::getRouteRegex('users')) : '';
                \Locales::isRoute('users/create') ? Route::get(\Locales::getRoute('users/create'), 'UserController@create')->name(\Locales::getRoutePrefix('users/create')) : '';
                Route::post(\Locales::getRoute('users/create'), 'UserController@store');
                \Locales::isRoute('users/destroy') ? Route::get(\Locales::getRoute('users/destroy'), 'UserController@confirm')->name(\Locales::getRoutePrefix('users/destroy')) : '';
                Route::delete(\Locales::getRoute('users/destroy'), 'UserController@destroy');
                \Locales::isTranslatedRoute('users/edit') ? Route::get(\Locales::getRoute('users/edit') . '/{user?}', 'UserController@edit')->name(\Locales::getRoutePrefix('users/edit'))->where('user', '[0-9]+') : '';
                \Locales::isTranslatedRoute('users/update') ? Route::put(\Locales::getRoute('users/update'), 'UserController@update')->name(\Locales::getRoutePrefix('users/update')) : '';

                \Locales::isRoute('clients') ? Route::get(\Locales::getRoute('clients'), 'UserController@index')->name(\Locales::getRoutePrefix('clients')) : '';
                \Locales::isRoute('clients/level1') ? Route::get(\Locales::getRoute('clients/level1'), 'UserController@index')->name(\Locales::getRoutePrefix('clients/level1')) : '';
                \Locales::isRoute('clients/level2') ? Route::get(\Locales::getRoute('clients/level2'), 'UserController@index')->name(\Locales::getRoutePrefix('clients/level2')) : '';

                \Locales::isRoute('profile') ? Route::get(\Locales::getRoute('profile'), 'PageController@pages')->name(\Locales::getRoutePrefix('profile')) : '';

                \Locales::isRoute('messages') ? Route::get(\Locales::getRoute('messages'), 'PageController@pages')->name(\Locales::getRoutePrefix('messages')) : '';

                \Locales::isRoute('settings') ? Route::get(\Locales::getRoute('settings'), 'PageController@pages')->name(\Locales::getRoutePrefix('settings')) : '';
            });
        }

    });
} elseif ($subdomain == 'www') {
    Route::group(['domain' => $subdomain . '.' . config('app.domain'), 'namespace' => ucfirst($subdomain)], function() {
        Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name('/');
    });
}
