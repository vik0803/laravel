<?php

/*
|--------------------------------------------------------------------------
| Dynamic Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    foreach (\Locales::getDomains() as $domain => $value) {
        if ($domain == 'cms') {
            \Locales::setRoutesDomain($domain);

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

                        Route::get(\Locales::getRoute('dashboard'), 'DashboardController@dashboard')->name(\Locales::getRoutePrefix('dashboard'));

                        Route::group(['middleware' => 'ajax'], function() {
                            \Locales::isTranslatedRoute('nav/create') ? Route::get(\Locales::getRoute('nav/create'), 'NavController@create')->name(\Locales::getRoutePrefix('nav/create')) : '';
                            \Locales::isTranslatedRoute('nav/create-category') ? Route::get(\Locales::getRoute('nav/create-category'), 'NavController@createCategory')->name(\Locales::getRoutePrefix('nav/create-category')) : '';
                            \Locales::isTranslatedRoute('nav/store') ? Route::post(\Locales::getRoute('nav/store'), 'NavController@store')->name(\Locales::getRoutePrefix('nav/store')) : '';
                            \Locales::isTranslatedRoute('nav/edit') ? Route::get(\Locales::getRoute('nav/edit') . '/{page?}', 'NavController@edit')->name(\Locales::getRoutePrefix('nav/edit'))->where('page', '[0-9]+') : '';
                            \Locales::isTranslatedRoute('nav/update') ? Route::put(\Locales::getRoute('nav/update'), 'NavController@update')->name(\Locales::getRoutePrefix('nav/update')) : '';
                            \Locales::isTranslatedRoute('nav/delete') ? Route::get(\Locales::getRoute('nav/delete'), 'NavController@delete')->name(\Locales::getRoutePrefix('nav/delete')) : '';
                            \Locales::isTranslatedRoute('nav/destroy') ? Route::delete(\Locales::getRoute('nav/destroy'), 'NavController@destroy')->name(\Locales::getRoutePrefix('nav/destroy')) : '';
                            \Locales::isTranslatedRoute('nav/delete-image') ? Route::get(\Locales::getRoute('nav/delete-image'), 'NavController@deleteImage')->name(\Locales::getRoutePrefix('nav/delete-image')) : '';
                            \Locales::isTranslatedRoute('nav/destroy-image') ? Route::delete(\Locales::getRoute('nav/destroy-image'), 'NavController@destroyImage')->name(\Locales::getRoutePrefix('nav/destroy-image')) : '';
                            \Locales::isTranslatedRoute('nav/edit-image') ? Route::get(\Locales::getRoute('nav/edit-image') . '/{image?}', 'NavController@editImage')->name(\Locales::getRoutePrefix('nav/edit-image'))->where('image', '[0-9]+') : '';
                            \Locales::isTranslatedRoute('nav/update-image') ? Route::put(\Locales::getRoute('nav/update-image'), 'NavController@updateImage')->name(\Locales::getRoutePrefix('nav/update-image')) : '';
                        });
                        \Locales::isTranslatedRoute('nav/upload') ? Route::post(\Locales::getRoute('nav/upload') . '/{chunk?}', 'NavController@upload')->name(\Locales::getRoutePrefix('nav/upload'))->where('chunk', 'done') : '';
                        \Locales::isTranslatedRoute('nav') ? Route::get(\Locales::getRoute('nav') . '/{slugs?}', 'NavController@index')->name(\Locales::getRoutePrefix('nav'))->where('slugs', '(.*)') : '';

                        Route::group(['middleware' => 'ajax'], function() {
                            \Locales::isTranslatedRoute('banners/create') ? Route::get(\Locales::getRoute('banners/create'), 'BannerController@create')->name(\Locales::getRoutePrefix('banners/create')) : '';
                            \Locales::isTranslatedRoute('banners/store') ? Route::post(\Locales::getRoute('banners/store'), 'BannerController@store')->name(\Locales::getRoutePrefix('banners/store')) : '';
                            \Locales::isTranslatedRoute('banners/edit') ? Route::get(\Locales::getRoute('banners/edit') . '/{banner?}', 'BannerController@edit')->name(\Locales::getRoutePrefix('banners/edit'))->where('banner', '[0-9]+') : '';
                            \Locales::isTranslatedRoute('banners/update') ? Route::put(\Locales::getRoute('banners/update'), 'BannerController@update')->name(\Locales::getRoutePrefix('banners/update')) : '';
                            \Locales::isTranslatedRoute('banners/delete') ? Route::get(\Locales::getRoute('banners/delete'), 'BannerController@delete')->name(\Locales::getRoutePrefix('banners/delete')) : '';
                            \Locales::isTranslatedRoute('banners/destroy') ? Route::delete(\Locales::getRoute('banners/destroy'), 'BannerController@destroy')->name(\Locales::getRoutePrefix('banners/destroy')) : '';
                            \Locales::isTranslatedRoute('banners/delete-image') ? Route::get(\Locales::getRoute('banners/delete-image'), 'BannerController@deleteImage')->name(\Locales::getRoutePrefix('banners/delete-image')) : '';
                            \Locales::isTranslatedRoute('banners/destroy-image') ? Route::delete(\Locales::getRoute('banners/destroy-image'), 'BannerController@destroyImage')->name(\Locales::getRoutePrefix('banners/destroy-image')) : '';
                            \Locales::isTranslatedRoute('banners/edit-image') ? Route::get(\Locales::getRoute('banners/edit-image') . '/{image?}', 'BannerController@editImage')->name(\Locales::getRoutePrefix('banners/edit-image'))->where('image', '[0-9]+') : '';
                            \Locales::isTranslatedRoute('banners/update-image') ? Route::put(\Locales::getRoute('banners/update-image'), 'BannerController@updateImage')->name(\Locales::getRoutePrefix('banners/update-image')) : '';
                        });
                        \Locales::isTranslatedRoute('banners/upload') ? Route::post(\Locales::getRoute('banners/upload') . '/{chunk?}', 'BannerController@upload')->name(\Locales::getRoutePrefix('banners/upload'))->where('chunk', 'done') : '';
                        \Locales::isTranslatedRoute('banners') ? Route::get(\Locales::getRoute('banners') . '/{slug?}', 'BannerController@index')->name(\Locales::getRoutePrefix('banners'))->where('slug', '[a-z-]+') : '';

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

});
