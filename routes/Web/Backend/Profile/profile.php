<?php
    Route::group([
        'namespace' => 'Backend\Audit',
        'prefix' => 'backend',
        'as' => 'backend.'
    ], function() {

        Route::group([ 'prefix' => 'profile',  'as' => 'profile.'], function() {
            Route::get('/profile-show', 'MyLogsController@profileShow')->name('show');
        });
    })->middleware('access.routeNeedsPermission:all_functions');
