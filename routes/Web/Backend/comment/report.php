<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/reports', 'ReportController@reports')->name('reports');
    });
})->middleware('access.routeNeedsPermission:case_worker');
