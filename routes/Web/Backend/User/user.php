<?php
Route::group([
    'namespace' => 'Backend',
    'prefix' => 'backend',
    'as' => 'backend.'
], function () {
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/index', 'UserCrudController@index')->name('index');
        Route::get('/user', 'UserCrudController@getAll')->name('user');
        Route::get('/active_manager', 'UserCrudController@activeManagers')->name('active_manager');
        Route::get('/get_staff_user_for_dt', 'UserCrudController@getAllForDt')->name('get_staff_user_for_dt');

        Route::get('/create', 'UserCrudController@create')->name('create');
        Route::post('/', 'UserCrudController@store')->name('store');
        Route::get('/{user}', 'UserCrudController@show')->name('show');
        Route::get('/edit/{user}', 'UserCrudController@edit')->name('edit');
        Route::put('/{user}', 'UserCrudController@update')->name('update');
        Route::delete('/{user}', 'UserCrudController@destroy')->name('destroy');

        Route::get('/user_by_specialization/{specialization}', 'UserCrudController@getBySpecialization')->name('user_by_specialization');
        Route::post('/{manager}/increment_favorite', 'UserCrudController@incrementFavorite')->name('increment_favorite');
    });
})->middleware('access.routeNeedsPermission:manage_user');
