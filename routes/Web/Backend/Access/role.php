<?php
Route::group([
    'namespace' => 'Backend\Access',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {


    Route::group(['prefix' => 'role', 'as' => 'role.'], function () {

        Route::get('/index', 'RoleController@index')->name('index');
        Route::get('/create', 'RoleController@create')->name('create');
        Route::post('/store', 'RoleController@store')->name('store');
        Route::get('/edit/{role}', 'RoleController@edit')->name('edit');
        Route::put('/update/{role}', 'RoleController@update')->name('update');
        Route::get('/profile/{role}', 'RoleController@profile')->name('profile');
        Route::delete('/delete/{role}', 'RoleController@delete')->name('delete');
        Route::get('/roles/users/{role}', 'RoleController@users')->name('users');
        Route::get('/get_all_for_dt', 'RoleController@getAllForDt')->name('get_all_for_dt');

    });
})->middleware('access.routeNeedsPermission:all_functions');
