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

        Route::get('/create', 'SaasAppController@create')->name('create');
        Route::post('/', 'SaasAppController@store')->name('store');
        Route::get('/{saas_app}', 'SaasAppController@show')->name('show');
        Route::get('/edit/{saas_app}', 'SaasAppController@edit')->name('edit');
        Route::put('/{saas_app}', 'SaasAppController@update')->name('update');
        Route::delete('/{saas_app}', 'SaasAppController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_saas_app');
