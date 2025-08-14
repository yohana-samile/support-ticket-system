<?php
Route::group([
    'namespace' => 'Backend\Access',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {

    Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {

        Route::get('/index', 'PermissionController@index')->name('index');
        Route::get('/get_all_for_dt', 'PermissionController@getAllForDt')->name('get_all_for_dt');

    });
})->middleware('access.routeNeedsPermission:manage_roles_permissions');
