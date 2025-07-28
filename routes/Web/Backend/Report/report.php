<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/index', 'ReportController@index')->name('index');
        Route::get('/data', 'ReportController@data')->name('data');
        Route::post('/export', 'ReportController@export')->name('export');
    });
})->middleware('access.routeNeedsPermission:manage_report');
