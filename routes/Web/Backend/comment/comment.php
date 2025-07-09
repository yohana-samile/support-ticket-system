<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
        Route::post('/tickets/{ticket}/comments', 'CommentController@store')->name('store');
        Route::delete('/comments/{comment}', 'CommentController@destroy')->name('destroy');
        Route::put('/comments/{comment}', 'CommentController@update')->name('update');
    });
})->middleware('access.routeNeedsPermission:comment');
