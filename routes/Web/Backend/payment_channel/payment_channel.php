<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'payment_channel', 'as' => 'payment_channel.'], function () {
        Route::get('/index', 'PaymentChannelController@index')->name('index');
        Route::get('/payment_channels', 'PaymentChannelController@getAll')->name('payment_channels');
        Route::get('/find_by_code', 'PaymentChannelController@findByCode')->name('find_by_code');
        Route::get('/active_payment_channels', 'PaymentChannelController@activeChannels')->name('active_payment_channels');

        Route::get('/create', 'PaymentChannelController@create')->name('create');
        Route::post('/', 'PaymentChannelController@store')->name('store');
        Route::get('/{paymentChannel}', 'PaymentChannelController@show')->name('show');
        Route::get('/edit/{paymentChannel}', 'PaymentChannelController@edit')->name('edit');
        Route::put('/{paymentChannel}', 'PaymentChannelController@update')->name('update');
        Route::delete('/{paymentChannel}', 'PaymentChannelController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_payment_channel');
