<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'tertiary', 'as' => 'tertiary.'], function () {
        Route::get('/index', 'TertiaryTopicController@index')->name('index');
        Route::get('/tertiary_topic', 'TertiaryTopicController@getAllForDt')->name('get_tertiary_topic_for_dt');
        Route::get('/tertiary_topic_by_subtopic_id/{subtopicUid}', 'TertiaryTopicController@getBySubtopic')->name('tertiary_topic_by_subtopic_id');
        Route::get('/search', 'TertiaryTopicController@search')->name('search');

        Route::get('/create', 'TertiaryTopicController@create')->name('create');
        Route::post('/', 'TertiaryTopicController@store')->name('store');
        Route::get('/show/{tertiary}', 'TertiaryTopicController@show')->name('show');
        Route::get('/edit/{tertiary}', 'TertiaryTopicController@edit')->name('edit');

        Route::put('/update/{tertiary}', 'TertiaryTopicController@update')->name('update');
        Route::delete('/destroy/{tertiary}', 'TertiaryTopicController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_tertiary_topics');
