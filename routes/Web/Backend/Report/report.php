<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/index', 'ReportController@index')->name('index');
        Route::get('/history', 'ReportController@history')->name('history');
        Route::get('/summary', 'ReportController@summary')->name('summary');
        Route::get('/saas_app_data', 'ReportController@saasAppData')->name('saas_app_data');

        Route::get('/data', 'ReportController@data')->name('data');
        Route::post('/export', 'ReportController@export')->name('export');
        Route::post('/export_summary', 'ReportController@exportSummary')->name('export_summary');
    });
})->middleware('access.routeNeedsPermission:manage_report');
