<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'sender_id', 'as' => 'sender_id.'], function () {
        Route::get('/index', 'SenderIdController@index')->name('index');
        Route::get('/search', 'SenderIdController@search')->name('search');

        Route::get('/active_sender_ids/{clientId}', 'SenderIdController@activeSenderIds')->name('active_sender_ids');
        Route::get('/sender_ids', 'SenderIdController@getAll')->name('sender_ids');
        Route::get('/get_sender_ids_for_dt', 'SenderIdController@getAllForDt')->name('get_sender_ids_for_dt');

        Route::get('/create', 'SenderIdController@create')->name('create');
        Route::post('/', 'SenderIdController@store')->name('store');

        Route::get('/show/{sender}', 'SenderIdController@show')->name('show');
        Route::get('/edit/{sender}', 'SenderIdController@edit')->name('edit');
        Route::put('/update/{sender}', 'SenderIdController@update')->name('update');
        Route::delete('/destroy/{sender}', 'SenderIdController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manager_sender_ids');
