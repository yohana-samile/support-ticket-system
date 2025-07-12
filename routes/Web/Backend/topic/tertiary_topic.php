<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'tertiary', 'as' => 'tertiary.'], function () {
        Route::get('/index', 'TertiaryTopicController@index')->name('index');
        Route::get('/tertiary-topic', 'TertiaryTopicController@getAll')->name('topic');
        Route::get('/tertiary_topic_by_subtopic_id/{subtopicUid}', 'TertiaryTopicController@getBySubtopic')->name('tertiary_topic_by_subtopic_id');

        Route::get('/create', 'TertiaryTopicController@create')->name('create');
        Route::post('/', 'TertiaryTopicController@store')->name('store');
        Route::get('/{topic}', 'TertiaryTopicController@show')->name('show');
        Route::get('/edit/{topic}', 'TertiaryTopicController@edit')->name('edit');
        Route::put('/{topic}', 'TertiaryTopicController@update')->name('update');
        Route::delete('/{topic}', 'TertiaryTopicController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_topic');
