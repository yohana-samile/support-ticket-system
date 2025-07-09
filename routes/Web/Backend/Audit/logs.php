<?php
Route::group([
    'namespace' => 'Backend\Audit',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {

    Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {

        Route::get('/index', 'LogController@index')->name('index');
        Route::get('/{fileName}', 'LogController@show')->name('show');
        Route::get('/{fileName}/download', 'LogController@download')->name('download');
        Route::get('/logs/{fileName}', 'LogController@delete')->where('fileName', '.*')->name('delete');

    });
})->middleware('access.routeNeedsPermission:all_functions');
