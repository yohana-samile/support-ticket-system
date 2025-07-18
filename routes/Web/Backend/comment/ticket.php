<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
        Route::get('/index', 'TicketController@index')->name('index');
        Route::get('/create', 'TicketController@create')->name('create');
        Route::post('/', 'TicketController@store')->name('store');
        Route::get('/{ticket}', 'TicketController@show')->name('show');
        Route::get('/edit/{ticket}', 'TicketController@edit')->name('edit');
        Route::put('/{ticket}', 'TicketController@update')->name('update');

        Route::put('/assigned', 'TicketController@assigned')->name('assigned');
        Route::post('/reassign/{ticket}', 'TicketController@reassign')->name('reassign');

        Route::delete('/{ticket}', 'TicketController@destroy')->name('destroy');
        Route::post('/update-status/{ticket}', 'TicketController@updateStatus')->name('update-status');
        Route::post('/attach-services/{ticket}', 'TicketController@attachServices')->name('attach-services');
        Route::get('/reports', 'TicketController@reports')->name('reports');
    });
})->middleware('access.routeNeedsPermission:case_worker');
