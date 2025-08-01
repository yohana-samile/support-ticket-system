<?php
Route::group([
    'namespace' => 'Frontend',
    'prefix' => 'frontend',
    'as' => 'frontend.'
], function () {
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
        Route::get('/index', 'TicketController@index')->name('index');
        Route::get('/create', 'TicketController@create')->name('create');
        Route::post('store/', 'TicketController@store')->name('store');

        Route::get('/ticket/edit/{ticket}', 'TicketController@edit')->name('edit');
        Route::put('/ticket/update/{ticket}', 'TicketController@update')->name('update');
        Route::delete('/ticket/update/{ticket}', 'TicketController@destroy')->name('destroy');
        Route::get('/show/{ticket}', 'TicketController@show')->name('show');

        Route::get('/feedback/{ticket}', 'TicketController@feedback')->name('feedback');
        Route::post('/feedback/{ticket}', 'TicketController@submitFeedback')->name('submit-feedback');

    });
})->middleware('access.routeNeedsPermission:reporter');
