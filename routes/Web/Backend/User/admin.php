<?php
Route::prefix('users')->middleware('access.routeNeedsPermission:all_functions')->group(function () {
    Route::get('/user', [\App\Http\Controllers\Backend\UserCrudController::class, 'index'])->name('backend.user.index');
    Route::get('/get-all-users', [\App\Http\Controllers\Backend\UserCrudController::class, 'fetchUser'])->name('backend.get.user');
    Route::get('/get-admin-users', [\App\Http\Controllers\Backend\UserCrudController::class, 'fetchAdminUser'])->name('backend.get.admin.user');

    Route::get('/create', [\App\Http\Controllers\Backend\UserCrudController::class, 'create'])->name('backend.create.user');
    Route::post('/create', [\App\Http\Controllers\Backend\UserCrudController::class, 'store'])->name('backend.create.user');
    Route::put('/update/{user}', [\App\Http\Controllers\Backend\UserCrudController::class, 'update'])->name('backend.update.user');
    Route::post('/delete-user/{user}', [\App\Http\Controllers\Backend\UserCrudController::class, 'deleteUser'])->name('backend.delete.user');

    Route::get('/show/{user}', [\App\Http\Controllers\Backend\UserCrudController::class, 'profile'])->name('backend.show.user');
    Route::get('/edit/{user}', [\App\Http\Controllers\Backend\UserCrudController::class, 'edit'])->name('backend.edit.user');
    Route::get('/activity/{user}', [\App\Http\Controllers\Backend\UserCrudController::class, 'activity'])->name('backend.user.activity');

});
