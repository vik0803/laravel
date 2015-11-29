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
                \Locales::isTranslatedRoute('register') ? Route::get(\Locales::getRoute('register'), 'AuthController@getRegister')->name(\Locales::getRoutePrefix('register')) : '';
                Route::post(\Locales::getRoute('register'), 'AuthController@postRegister');

                Route::get(\Locales::getRoute('signout'), 'AuthController@getLogout')->name(\Locales::getRoutePrefix('signout'));

                Route::get(\Locales::getRoute(\Config::get('app.defaultAuthRoute')), 'PageController@' . \Config::get('app.defaultAuthRoute'))->name(\Locales::getRoutePrefix(\Config::get('app.defaultAuthRoute')));

                \Locales::isTranslatedRoute('pages') ? Route::get(\Locales::getRoute('pages'), 'PageController@pages')->name(\Locales::getRoutePrefix('pages')) : '';

                \Locales::isTranslatedRoute('users') ? Route::get(\Locales::getRoute('users') . '/{group?}', 'UserController@index')->name(\Locales::getRoutePrefix('users'))->where('group', \Locales::getRouteRegex('users')) : '';
                \Locales::isTranslatedRoute('users/create') ? Route::get(\Locales::getRoute('users/create'), 'UserController@create')->name(\Locales::getRoutePrefix('users/create')) : '';
                \Locales::isTranslatedRoute('users/store') ? Route::post(\Locales::getRoute('users/store'), 'UserController@store')->name(\Locales::getRoutePrefix('users/store')) : '';
                \Locales::isTranslatedRoute('users/edit') ? Route::get(\Locales::getRoute('users/edit') . '/{user?}', 'UserController@edit')->name(\Locales::getRoutePrefix('users/edit'))->where('user', '[0-9]+') : '';
                \Locales::isTranslatedRoute('users/update') ? Route::put(\Locales::getRoute('users/update'), 'UserController@update')->name(\Locales::getRoutePrefix('users/update')) : '';
                \Locales::isTranslatedRoute('users/delete') ? Route::get(\Locales::getRoute('users/delete'), 'UserController@delete')->name(\Locales::getRoutePrefix('users/delete')) : '';
                \Locales::isTranslatedRoute('users/destroy') ? Route::delete(\Locales::getRoute('users/destroy'), 'UserController@destroy')->name(\Locales::getRoutePrefix('users/destroy')) : '';

                \Locales::isTranslatedRoute('settings/domains') ? Route::get(\Locales::getRoute('settings/domains'), 'DomainController@index')->name(\Locales::getRoutePrefix('settings/domains')) : '';
                \Locales::isTranslatedRoute('settings/locales') ? Route::get(\Locales::getRoute('settings/locales'), 'LocaleController@index')->name(\Locales::getRoutePrefix('settings/locales')) : '';

                \Locales::isTranslatedRoute('profile') ? Route::get(\Locales::getRoute('profile'), 'PageController@pages')->name(\Locales::getRoutePrefix('profile')) : '';

                \Locales::isTranslatedRoute('messages') ? Route::get(\Locales::getRoute('messages'), 'PageController@pages')->name(\Locales::getRoutePrefix('messages')) : '';
            });
        }

    });
} elseif ($subdomain == 'www') {
    Route::group(['domain' => $subdomain . '.' . config('app.domain'), 'namespace' => ucfirst($subdomain)], function() {
        Route::get(\Locales::getRoute('/'), 'AuthController@getLogin')->name('/');
    });
}
