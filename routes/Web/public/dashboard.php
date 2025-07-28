<?php
    Route::prefix('frontend')->middleware('access.routeNeedsPermission:reporter')->group(function () {
        Route::get('/layouts/dashboard', function () {
            return view('dashboard.frontend.dashboard');
        })->name('frontend.dashboard');
    });

    Route::prefix('backend')->group(function () {
        Route::get('/layouts/dashboard', function () {
            return view('dashboard.backend.dashboard');
        })->name('backend.dashboard');
    });
