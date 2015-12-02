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
                \Locales::isTranslatedRoute('settings/domains/create') ? Route::get(\Locales::getRoute('settings/domains/create'), 'DomainController@create')->name(\Locales::getRoutePrefix('settings/domains/create')) : '';
                \Locales::isTranslatedRoute('settings/domains/store') ? Route::post(\Locales::getRoute('settings/domains/store'), 'DomainController@store')->name(\Locales::getRoutePrefix('settings/domains/store')) : '';
                \Locales::isTranslatedRoute('settings/domains/edit') ? Route::get(\Locales::getRoute('settings/domains/edit') . '/{domain?}', 'DomainController@edit')->name(\Locales::getRoutePrefix('settings/domains/edit'))->where('domain', '[0-9]+') : '';
                \Locales::isTranslatedRoute('settings/domains/update') ? Route::put(\Locales::getRoute('settings/domains/update'), 'DomainController@update')->name(\Locales::getRoutePrefix('settings/domains/update')) : '';
                \Locales::isTranslatedRoute('settings/domains/delete') ? Route::get(\Locales::getRoute('settings/domains/delete'), 'DomainController@delete')->name(\Locales::getRoutePrefix('settings/domains/delete')) : '';
                \Locales::isTranslatedRoute('settings/domains/destroy') ? Route::delete(\Locales::getRoute('settings/domains/destroy'), 'DomainController@destroy')->name(\Locales::getRoutePrefix('settings/domains/destroy')) : '';

                \Locales::isTranslatedRoute('settings/locales') ? Route::get(\Locales::getRoute('settings/locales'), 'LocaleController@index')->name(\Locales::getRoutePrefix('settings/locales')) : '';
                \Locales::isTranslatedRoute('settings/locales/create') ? Route::get(\Locales::getRoute('settings/locales/create'), 'LocaleController@create')->name(\Locales::getRoutePrefix('settings/locales/create')) : '';
                \Locales::isTranslatedRoute('settings/locales/store') ? Route::post(\Locales::getRoute('settings/locales/store'), 'LocaleController@store')->name(\Locales::getRoutePrefix('settings/locales/store')) : '';
                \Locales::isTranslatedRoute('settings/locales/edit') ? Route::get(\Locales::getRoute('settings/locales/edit') . '/{locale?}', 'LocaleController@edit')->name(\Locales::getRoutePrefix('settings/locales/edit'))->where('locale', '[0-9]+') : '';
                \Locales::isTranslatedRoute('settings/locales/update') ? Route::put(\Locales::getRoute('settings/locales/update'), 'LocaleController@update')->name(\Locales::getRoutePrefix('settings/locales/update')) : '';
                \Locales::isTranslatedRoute('settings/locales/delete') ? Route::get(\Locales::getRoute('settings/locales/delete'), 'LocaleController@delete')->name(\Locales::getRoutePrefix('settings/locales/delete')) : '';
                \Locales::isTranslatedRoute('settings/locales/destroy') ? Route::delete(\Locales::getRoute('settings/locales/destroy'), 'LocaleController@destroy')->name(\Locales::getRoutePrefix('settings/locales/destroy')) : '';

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
