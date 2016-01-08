<?php

/*
|--------------------------------------------------------------------------
| Dynamic Application Routes
|--------------------------------------------------------------------------
*/

foreach (\Locales::getDomains() as $domain => $value) {
    if ($domain == 'cms') {
        Route::group(['domain' => $domain . '.' . config('app.domain'), 'namespace' => ucfirst($domain)], function() use ($value) {

            foreach ($value->locales as $locale) {
                \Locales::setRoutesLocale($locale->locale);

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

                    Route::get(\Locales::getRoute(\Config::get('app.defaultAuthRoute')), 'DashboardController@' . \Config::get('app.defaultAuthRoute'))->name(\Locales::getRoutePrefix(\Config::get('app.defaultAuthRoute')));

                    Route::group(['middleware' => 'ajax'], function() {
                        \Locales::isTranslatedRoute('pages/create') ? Route::get(\Locales::getRoute('pages/create'), 'PageController@create')->name(\Locales::getRoutePrefix('pages/create')) : '';
                        \Locales::isTranslatedRoute('pages/create-category') ? Route::get(\Locales::getRoute('pages/create-category'), 'PageController@createCategory')->name(\Locales::getRoutePrefix('pages/create-category')) : '';
                        \Locales::isTranslatedRoute('pages/store') ? Route::post(\Locales::getRoute('pages/store'), 'PageController@store')->name(\Locales::getRoutePrefix('pages/store')) : '';
                        \Locales::isTranslatedRoute('pages/edit') ? Route::get(\Locales::getRoute('pages/edit') . '/{page?}', 'PageController@edit')->name(\Locales::getRoutePrefix('pages/edit'))->where('page', '[0-9]+') : '';
                        \Locales::isTranslatedRoute('pages/update') ? Route::put(\Locales::getRoute('pages/update'), 'PageController@update')->name(\Locales::getRoutePrefix('pages/update')) : '';
                        \Locales::isTranslatedRoute('pages/delete') ? Route::get(\Locales::getRoute('pages/delete'), 'PageController@delete')->name(\Locales::getRoutePrefix('pages/delete')) : '';
                        \Locales::isTranslatedRoute('pages/destroy') ? Route::delete(\Locales::getRoute('pages/destroy'), 'PageController@destroy')->name(\Locales::getRoutePrefix('pages/destroy')) : '';
                    });
                    \Locales::isTranslatedRoute('pages') ? Route::get(\Locales::getRoute('pages') . '/{categories?}', 'PageController@index')->name(\Locales::getRoutePrefix('pages'))->where('categories', '(.*)') : '';

                    \Locales::isTranslatedRoute('users') ? Route::get(\Locales::getRoute('users') . '/{group?}', 'UserController@index')->name(\Locales::getRoutePrefix('users'))->where('group', \Locales::getRouteRegex('users')) : '';
                    Route::group(['middleware' => 'ajax'], function() {
                        \Locales::isTranslatedRoute('users/create') ? Route::get(\Locales::getRoute('users/create'), 'UserController@create')->name(\Locales::getRoutePrefix('users/create')) : '';
                        \Locales::isTranslatedRoute('users/store') ? Route::post(\Locales::getRoute('users/store'), 'UserController@store')->name(\Locales::getRoutePrefix('users/store')) : '';
                        \Locales::isTranslatedRoute('users/edit') ? Route::get(\Locales::getRoute('users/edit') . '/{user?}', 'UserController@edit')->name(\Locales::getRoutePrefix('users/edit'))->where('user', '[0-9]+') : '';
                        \Locales::isTranslatedRoute('users/update') ? Route::put(\Locales::getRoute('users/update'), 'UserController@update')->name(\Locales::getRoutePrefix('users/update')) : '';
                        \Locales::isTranslatedRoute('users/delete') ? Route::get(\Locales::getRoute('users/delete'), 'UserController@delete')->name(\Locales::getRoutePrefix('users/delete')) : '';
                        \Locales::isTranslatedRoute('users/destroy') ? Route::delete(\Locales::getRoute('users/destroy'), 'UserController@destroy')->name(\Locales::getRoutePrefix('users/destroy')) : '';
                    });

                    \Locales::isTranslatedRoute('settings/domains') ? Route::get(\Locales::getRoute('settings/domains'), 'DomainController@index')->name(\Locales::getRoutePrefix('settings/domains')) : '';
                    Route::group(['middleware' => 'ajax'], function() {
                        \Locales::isTranslatedRoute('settings/domains/create') ? Route::get(\Locales::getRoute('settings/domains/create'), 'DomainController@create')->name(\Locales::getRoutePrefix('settings/domains/create')) : '';
                        \Locales::isTranslatedRoute('settings/domains/store') ? Route::post(\Locales::getRoute('settings/domains/store'), 'DomainController@store')->name(\Locales::getRoutePrefix('settings/domains/store')) : '';
                        \Locales::isTranslatedRoute('settings/domains/edit') ? Route::get(\Locales::getRoute('settings/domains/edit') . '/{domain?}', 'DomainController@edit')->name(\Locales::getRoutePrefix('settings/domains/edit'))->where('domain', '[0-9]+') : '';
                        \Locales::isTranslatedRoute('settings/domains/update') ? Route::put(\Locales::getRoute('settings/domains/update'), 'DomainController@update')->name(\Locales::getRoutePrefix('settings/domains/update')) : '';
                        \Locales::isTranslatedRoute('settings/domains/delete') ? Route::get(\Locales::getRoute('settings/domains/delete'), 'DomainController@delete')->name(\Locales::getRoutePrefix('settings/domains/delete')) : '';
                        \Locales::isTranslatedRoute('settings/domains/destroy') ? Route::delete(\Locales::getRoute('settings/domains/destroy'), 'DomainController@destroy')->name(\Locales::getRoutePrefix('settings/domains/destroy')) : '';
                    });

                    \Locales::isTranslatedRoute('settings/locales') ? Route::get(\Locales::getRoute('settings/locales'), 'LocaleController@index')->name(\Locales::getRoutePrefix('settings/locales')) : '';
                    Route::group(['middleware' => 'ajax'], function() {
                        \Locales::isTranslatedRoute('settings/locales/create') ? Route::get(\Locales::getRoute('settings/locales/create'), 'LocaleController@create')->name(\Locales::getRoutePrefix('settings/locales/create')) : '';
                        \Locales::isTranslatedRoute('settings/locales/store') ? Route::post(\Locales::getRoute('settings/locales/store'), 'LocaleController@store')->name(\Locales::getRoutePrefix('settings/locales/store')) : '';
                        \Locales::isTranslatedRoute('settings/locales/edit') ? Route::get(\Locales::getRoute('settings/locales/edit') . '/{locale?}', 'LocaleController@edit')->name(\Locales::getRoutePrefix('settings/locales/edit'))->where('locale', '[0-9]+') : '';
                        \Locales::isTranslatedRoute('settings/locales/update') ? Route::put(\Locales::getRoute('settings/locales/update'), 'LocaleController@update')->name(\Locales::getRoutePrefix('settings/locales/update')) : '';
                        \Locales::isTranslatedRoute('settings/locales/delete') ? Route::get(\Locales::getRoute('settings/locales/delete'), 'LocaleController@delete')->name(\Locales::getRoutePrefix('settings/locales/delete')) : '';
                        \Locales::isTranslatedRoute('settings/locales/destroy') ? Route::delete(\Locales::getRoute('settings/locales/destroy'), 'LocaleController@destroy')->name(\Locales::getRoutePrefix('settings/locales/destroy')) : '';
                    });
                });
            }

        });
    } elseif ($domain == 'www') {
        Route::group(['domain' => $domain . '.' . config('app.domain'), 'namespace' => ucfirst($domain)], function() use ($value) {

            foreach ($value->locales as $locale) {
                \Locales::setRoutesLocale($locale->locale);

                Route::group(['middleware' => 'guest'], function() {

                });

                Route::group(['middleware' => 'auth'], function() {

                });
            }

        });
    }
}
