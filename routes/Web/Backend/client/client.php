<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'client', 'as' => 'client.'], function () {
        Route::get('/index', 'ClientController@index')->name('index');
        Route::get('/get_client_for_dt', 'ClientController@getAllForDt')->name('get_client_for_dt');
        Route::get('/search', 'ClientController@search')->name('search');

        Route::get('/create', 'ClientController@create')->name('create');
        Route::post('/client', 'ClientController@store')->name('store');
        Route::put('/update_password/{client}', 'ClientController@updatePassword')->name('update_password');

        Route::post('/assign_sender_id/{client}', 'ClientController@assignSenderId')->name('assign_sender_id');
        Route::delete('/detach_sender_id/{client}/{senderId}', 'ClientController@detachSenderId')->name('detach_sender_id');

        Route::get('/client/{client}', 'ClientController@show')->name('show');
        Route::get('/client/edit/{client}', 'ClientController@edit')->name('edit');
        Route::put('/client/{client}', 'ClientController@update')->name('update');

        Route::delete('/client/{client}', 'ClientController@destroy')->name('destroy');

        Route::get('/client_by_services/{serviceId}', 'ClientController@getByService')->name('client_by_services');
    });
})->middleware('access.routeNeedsPermission:manage_client');
