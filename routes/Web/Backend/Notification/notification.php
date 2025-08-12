<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'notifications', 'as' => 'notifications.'], function () {
        Route::get('/index', 'UserCrudController@index')->name('index');
        Route::get('/read', 'UserCrudController@getAll')->name('read');
        Route::get('/show/{notification}', 'UserCrudController@getAll')->name('show');
    });
});
