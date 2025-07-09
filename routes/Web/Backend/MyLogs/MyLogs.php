<?php
    Route::group([
        'namespace' => 'Backend\Audit',
        'prefix' => 'backend',
        'as' => 'backend.'
    ], function () {

        Route::group(['prefix' => 'my_logs', 'as' => 'my_logs.'], function () {

            Route::get('/index', 'MyLogsController@index')->name('index');
            Route::get('/profile/{audit}', 'MyLogsController@profile')->name('profile');
             Route::get('/get_all_for_dt', 'MyLogsController@getAllForDt')->name('get_all_for_dt');
        });
    })->middleware('access.routeNeedsPermission:all_functions');
