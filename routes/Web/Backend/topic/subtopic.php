<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'subtopic', 'as' => 'subtopic.'], function () {
        Route::get('/index', 'SubtopicController@index')->name('index');
        Route::get('/subtopic', 'SubtopicController@getAll')->name('subtopic');
        Route::get('/tertiary_topic', 'SubtopicController@getAllForDt')->name('get_tertiary_topic_for_dt');

        Route::get('/get_by_topic_id/{topic}', 'SubtopicController@getByTopic')->name('get_by_topic_id');
        Route::get('/search', 'SubtopicController@search')->name('search');

        Route::get('/create', 'SubtopicController@create')->name('create');
        Route::post('/store/', 'SubtopicController@store')->name('store');

        Route::get('/show/{subtopic}', 'SubtopicController@show')->name('show');
        Route::get('/show_for_tertiary_topic/{subtopicId}', 'SubtopicController@showForTertiaryTopic')->name('show_for_tertiary_topic');

        Route::get('/edit/{subtopic}', 'SubtopicController@edit')->name('edit');
        Route::put('/update/{subtopic}', 'SubtopicController@update')->name('update');
        Route::delete('/destroy/{subtopic}', 'SubtopicController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_topic');
