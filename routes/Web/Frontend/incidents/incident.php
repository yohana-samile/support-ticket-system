<?php
Route::group([
    'namespace' => 'Frontend',
    'prefix' => 'frontend',
    'as' => 'frontend.'
], function () {
    Route::group(['prefix' => 'incident', 'as' => 'incident.'], function () {
        Route::get('/index', 'IncidentController@index')->name('index');
        Route::get('/create', 'IncidentController@create')->name('create');
        Route::post('/', 'IncidentController@store')->name('store');
        Route::get('/{incident}', 'IncidentController@show')->name('show');
        Route::get('/edit/{incident}', 'IncidentController@edit')->name('edit');
        Route::put('/{incident}', 'IncidentController@update')->name('update');
    });
})->middleware('access.routeNeedsPermission:reporter');
