<?php
    Route::group([
        'namespace' => 'Backend\Audit',
        'prefix' => 'backend',
        'as' => 'backend.'
    ], function () {

        Route::group(['prefix' => 'audit', 'as' => 'audit.'], function () {
            Route::get('/index', 'AuditController@index')->name('index');
            Route::get('/profile/{audit}', 'AuditController@profile')->name('profile');
             Route::get('/get_all_for_dt', 'AuditController@getAllForDt')->name('get_all_for_dt');
        });
    })->middleware('access.routeNeedsPermission:all_functions');
