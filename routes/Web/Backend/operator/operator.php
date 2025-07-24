<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'operator', 'as' => 'operator.'], function () {
        Route::get('/index', 'OperatorController@index')->name('index');
        Route::get('/get_all_operator', 'OperatorController@getAll')->name('get_all_operator');
        Route::get('/get_operator_for_dt', 'OperatorController@getAllForDt')->name('get_operator_for_dt');
    });
})->middleware('access.routeNeedsPermission:manage_operator');
