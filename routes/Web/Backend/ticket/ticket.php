<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
        Route::get('/index', 'TicketController@index')->name('index');
        Route::get('/get_ticket_for_dt', 'TicketController@getAllForDt')->name('get_ticket_for_dt');

        Route::get('/create', 'TicketController@create')->name('create');
        Route::post('/store', 'TicketController@store')->name('store');
        Route::get('/{ticket}', 'TicketController@show')->name('show');
        Route::get('/edit/{ticket}', 'TicketController@edit')->name('edit');
        Route::put('/{ticket}', 'TicketController@update')->name('update');

        Route::put('/assigned', 'TicketController@assigned')->name('assigned');
        Route::post('/reassign/{ticket}', 'TicketController@reassign')->name('reassign');

        Route::delete('/{ticket}', 'TicketController@destroy')->name('destroy');
        Route::delete('/attachment/{ticket}', 'TicketController@destroyAttachment')->name('destroy_attachment');

        Route::post('/update-status/{ticket}', 'TicketController@updateStatus')->name('update-status');
        Route::get('/resolve/{ticket}', 'TicketController@resolveViaEmail')->name('resolve.via.email')->middleware('signed');

        Route::post('/attach-services/{ticket}', 'TicketController@attachServices')->name('attach-services');
        Route::get('/reports', 'TicketController@reports')->name('reports');

        Route::get('/client_ticket_history/{clientId}', 'TicketController@getClientHistory')->name('client_ticket_history');
    });
})->middleware('access.routeNeedsPermission:case_worker');
