<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'attachment', 'as' => 'attachment.'], function () {
        Route::get('/view/{attachment}', 'AttachmentController@view')->name('view');
        Route::get('/attachment/{attachment}', 'AttachmentController@download')->name('download');
        Route::delete('/attachment/{attachment}', 'AttachmentController@destroy')->name('destroy');
    });
});
