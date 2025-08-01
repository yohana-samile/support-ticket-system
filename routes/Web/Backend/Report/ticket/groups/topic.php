<?php
Route::group([
    'namespace' => 'Backend\Report\Ticket\Group',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'report/ticket/group', 'as' => 'report.'], function () {
        Route::get('/by_topic', 'TopicGroupController@byTopic')->name('by_topic');
        Route::get('/topic_data', 'TopicGroupController@topicData')->name('topic_data');
        Route::get('/list_by_topic/{topic}', 'TopicGroupController@ticketsByTopic')->name('list_by_topic');

        Route::get('/topic_data', 'TopicGroupController@getTopicSummary')->name('topic_data');
    });
})->middleware('access.routeNeedsPermission:manage_report');
