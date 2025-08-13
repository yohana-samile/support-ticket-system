<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/index', 'SettingController@index')->name('index');
        Route::put('/update', 'SettingController@update')->name('update');
    });
});
