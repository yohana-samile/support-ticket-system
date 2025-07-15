<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'comment', 'as' => 'comment.'], function () {
        Route::post('/ticket/comment/{ticket}', 'CommentController@store')->name('store');
        Route::delete('/comment/{comment}', 'CommentController@destroy')->name('destroy');
        Route::put('/comment/{comment}', 'CommentController@update')->name('update');
    });
})->middleware('access.routeNeedsPermission:comment');
