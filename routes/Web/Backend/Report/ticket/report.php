<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::group(['prefix' => 'ticket'], function () {
            Route::get('/index', 'ReportController@index')->name('index');
            Route::get('/all_reports', 'ReportController@allReport')->name('all_reports');
            Route::get('/by_filter', 'ReportController@byFilter')->name('by_filter');

            Route::get('/data', 'ReportController@data')->name('data');
            Route::post('/export', 'ReportController@export')->name('export');
            Route::post('/export_summary', 'ReportController@exportSummary')->name('export_summary');




            Route::get('/report_by', 'ReportController@reportBy')->name('report_by');

        });
    });
})->middleware('access.routeNeedsPermission:manage_report');
