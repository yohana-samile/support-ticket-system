<?php
Route::group([
    'namespace' => 'Backend\Report\Ticket\Group',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report/ticket/group', 'as' => 'report.'], function () {
        Route::get('/by_mno', 'MnoReportGroupController@byMno')->name('by_mno');
        Route::get('/mno_data', 'MnoReportGroupController@mnoData')->name('mno_data');
        Route::get('/list_by_mno/{mno}', 'MnoReportGroupController@ticketsByMno')->name('list_by_mno');
    });
})->middleware('access.routeNeedsPermission:manage_report');
