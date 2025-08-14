<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'topic', 'as' => 'topic.'], function () {
        Route::get('/index', 'TopicController@index')->name('index');
        Route::get('/topic', 'TopicController@getAll')->name('topic');
        Route::get('/get_tertiary_topic_for_dt', 'TopicController@getAllForDt')->name('get_tertiary_topic_for_dt');

        Route::get('/search', 'TopicController@search')->name('search');
        Route::get('/get_by_service/{service}', 'TopicController@getByService')->name('get_by_service');

        Route::get('/create', 'TopicController@create')->name('create');
        Route::post('store/', 'TopicController@store')->name('store');
        Route::get('/show/{topic}', 'TopicController@show')->name('show');
        Route::get('/edit/{topic}', 'TopicController@edit')->name('edit');

        Route::put('/update/{topic}', 'TopicController@update')->name('update');
        Route::delete('/destroy/{topic}', 'TopicController@destroy')->name('destroy');
        Route::get('/topic_by_services/{topic}', 'TopicController@getByService')->name('topic_by_services');
    });
})->middleware('access.routeNeedsPermission:manage_topics');
