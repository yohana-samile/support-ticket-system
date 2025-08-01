<?php
Route::group([
    'namespace' => 'Backend\Report\Ticket\Group',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report/ticket/group', 'as' => 'report.'], function () {
        Route::get('/by_saas_app', 'SaasAppGroupController@bySaasApp')->name('by_saas_app');
        Route::get('/saas_app_data', 'SaasAppGroupController@saasAppData')->name('saas_app_data');
        Route::get('/list_by_saas_app/{saas}', 'SaasAppGroupController@ticketsBySaasApp')->name('list_by_saas_app');

        Route::get('/saas_app_data', 'SaasAppGroupController@saasAppData')->name('saas_app_data');
    });
})->middleware('access.routeNeedsPermission:manage_report');
