<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/index', 'ReportController@index')->name('index');
        Route::get('/all_reports', 'ReportController@allReport')->name('all_reports');
        Route::get('/by_saas_app', 'ReportController@BySaasApp')->name('by_saas_app');
        Route::get('/by_topic', 'ReportController@byTopic')->name('by_topic');
        Route::get('/by_payment_channel', 'ReportController@byPaymentChannel')->name('by_payment_channel');
        Route::get('/by_filter', 'ReportController@byFilter')->name('by_filter');


        Route::get('/report_by', 'ReportController@reportBy')->name('report_by');
//        Route::get('/history', 'ReportController@history')->name('history');
        Route::get('/summary', 'ReportController@summary')->name('summary');


        Route::get('/saas_app_data', 'ReportController@saasAppData')->name('saas_app_data');
        Route::get('/topic_data', 'ReportController@getTopicSummary')->name('topic_data');
        Route::get('/payment_channel_data', 'ReportController@getPaymentChannelSummary')->name('payment_channel_data');

        Route::get('/data', 'ReportController@data')->name('data');
        Route::post('/export', 'ReportController@export')->name('export');
        Route::post('/export_summary', 'ReportController@exportSummary')->name('export_summary');
    });
})->middleware('access.routeNeedsPermission:manage_report');
