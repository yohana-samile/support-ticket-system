<?php
Route::group([
    'namespace' => 'Backend\Report\Ticket\Group',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report/ticket/group', 'as' => 'report.'], function () {
        Route::get('/by_client', 'ClientGroupController@byClient')->name('by_client');
        Route::get('/client_data', 'ClientGroupController@clientData')->name('client_data');
        Route::get('/list_by_client/{client}', 'ClientGroupController@ticketsByClient')->name('list_by_client');

        Route::get('/client_data_summary', 'ClientGroupController@getClientSummary')->name('client_data_summary');
        Route::get('/export_ticket_by_client', 'ClientGroupController@exportTicketByClient')->name('export_ticket_by_client');
    });
})->middleware('access.routeNeedsPermission:manage_report');
