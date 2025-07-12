<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'sender_id', 'as' => 'sender_id.'], function () {
        Route::get('/index', 'SenderIdController@index')->name('index');
        Route::get('/active_sender_ids', 'SenderIdController@activeSenderIds')->name('active_sender_ids');
        Route::get('/sender_ids', 'SenderIdController@getAll')->name('sender_ids');

        Route::get('/create', 'SenderIdController@create')->name('create');
        Route::post('/', 'SenderIdController@store')->name('store');

        Route::get('/{senderId}', 'SenderIdController@show')->name('show');
        Route::get('/edit/{senderId}', 'SenderIdController@edit')->name('edit');
        Route::put('/{senderId}', 'SenderIdController@update')->name('update');
        Route::delete('/{senderId}', 'SenderIdController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manager_sender_ids');
