<?php
Route::group([
    'namespace' => 'Backend\Report\Ticket\Group',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report/ticket/group', 'as' => 'report.'], function () {
        Route::get('/by_payment_channel', 'PaymentChannelGroupController@byPaymentChannel')->name('by_payment_channel');
        Route::get('/payment_channel_data', 'PaymentChannelGroupController@paymentChannelData')->name('payment_channel_data');
        Route::get('/list_by_payment_channel/{channel}', 'PaymentChannelGroupController@ticketsByPaymentChannel')->name('list_by_payment_channel');
    });
})->middleware('access.routeNeedsPermission:manage_report');
