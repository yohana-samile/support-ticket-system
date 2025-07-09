<?php
    Route::group([
        'namespace' => 'Backend\User',
        'prefix' => 'admin',
        'as' => 'admin.'
    ], function () {

        Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {
            Route::get('/index', 'UserStaffController@index')->name('index');
            Route::get('/create', 'UserStaffController@create')->name('create');
            Route::post('/store', 'UserStaffController@store')->name('store');
            Route::get('/edit/{staff}', 'UserStaffController@edit')->name('edit');
            Route::put('/update/{staff}', 'UserStaffController@update')->name('update');
            Route::get('/profile/{staff}', 'UserStaffController@profile')->name('profile');
            Route::delete('/delete/{staff}', 'UserStaffController@delete')->name('delete');
            Route::get('/get_all_for_dt', 'UserStaffController@getAllForDt')->name('get_all_for_dt');
        });
    })->middleware('access.routeNeedsPermission:all_functions');

