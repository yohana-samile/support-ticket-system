<?php
Route::group([
    'namespace' => 'Frontend',
    'prefix' => 'frontend',
    'as' => 'frontend.'
], function () {
    Route::group(['prefix' => 'ticket', 'as' => 'ticket.'], function () {
        Route::get('/index', 'TicketController@index')->name('index');
        Route::get('/create', 'TicketController@create')->name('create');
        Route::post('/', 'TicketController@store')->name('store');
        Route::get('/{ticket}', 'TicketController@show')->name('show');

        Route::get('/feedback/{ticket}', 'TicketController@feedback')->name('feedback');
        Route::post('/feedback/{ticket}', 'TicketController@submitFeedback')->name('submit-feedback');

    });
})->middleware('access.routeNeedsPermission:reporter');
