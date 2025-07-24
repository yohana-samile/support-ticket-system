<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'saas_app', 'as' => 'saas_app.'], function () {
        Route::get('/index', 'SaasAppController@index')->name('index');
        Route::get('/saas_app', 'SaasAppController@getAll')->name('saas_app');
        Route::get('/saas_app', 'SaasAppController@search')->name('search');
        Route::get('/get_saas_app_for_dt', 'SaasAppController@getAllForDt')->name('get_saas_app_for_dt');

        Route::get('/create', 'SaasAppController@create')->name('create');
        Route::post('/', 'SaasAppController@store')->name('store');
        Route::get('/show/{saasApp}', 'SaasAppController@show')->name('show');

        Route::get('/edit/{saasApp}', 'SaasAppController@edit')->name('edit');
        Route::put('/update/{saasApp}', 'SaasAppController@update')->name('update');
        Route::delete('/destroy/{saasApp}', 'SaasAppController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_saas_app');
