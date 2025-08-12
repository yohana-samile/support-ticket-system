<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'stickers', 'as' => 'stickers.'], function () {
        Route::get('/index', 'StickerController@index')->name('index');
        Route::get('/get_stickers_for_dt', 'StickerController@getAllForDt')->name('get_stickers_for_dt');

        Route::get('/create', 'StickerController@create')->name('create');
        Route::post('/store', 'StickerController@store')->name('store');

        Route::get('/edit/{sticker}', 'StickerController@edit')->name('edit');
        Route::put('/update/{sticker}', 'StickerController@update')->name('update');
        Route::delete('/destroy/{sticker}', 'StickerController@delete')->name('destroy');

        Route::get('/read', 'StickerController@read')->name('read');
        Route::get('/show/{sticker}', 'StickerController@show')->name('show');
    });
});
