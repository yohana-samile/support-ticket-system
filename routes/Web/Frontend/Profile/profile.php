<?php
    Route::group([
        'namespace' => 'Frontend',
        'prefix' => 'frontend',
        'as' => 'frontend.'
    ], function() {

        Route::group([ 'prefix' => 'profile',  'as' => 'profile.'], function() {
            Route::get('/profile-show', 'MyLogsController@profileShow')->name('show');
        });
    })->middleware('access.routeNeedsPermission:reporter');
