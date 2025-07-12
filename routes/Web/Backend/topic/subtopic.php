<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'subtopic', 'as' => 'subtopic.'], function () {
        Route::get('/index', 'SubtopicController@index')->name('index');
        Route::get('/subtopic', 'SubtopicController@getAll')->name('subtopic');
        Route::get('/get_by_topic_id/{topic}', 'SubtopicController@getByTopic')->name('get_by_topic_id');

        Route::get('/create', 'SubtopicController@create')->name('create');
        Route::post('/', 'SubtopicController@store')->name('store');
        Route::get('/{subtopic}', 'SubtopicController@show')->name('show');
        Route::get('/edit/{subtopic}', 'SubtopicController@edit')->name('edit');
        Route::put('/{subtopic}', 'SubtopicController@update')->name('update');
        Route::delete('/{subtopic}', 'SubtopicController@destroy')->name('destroy');
    });
})->middleware('access.routeNeedsPermission:manage_topic');
