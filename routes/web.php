<?php

use App\Http\Controllers\Backend\ClientController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    ForgotPasswordController,
    RegisterController,
    LoginController
};
use App\Http\Controllers\AdminController;

Route::group(['middleware' => 'web'], function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/log_me_in', 'logMeIn')->name('log_me_in');
        Route::post('/logout', 'logout')->name('logout');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register');
        Route::post('/signup', 'signup')->name('signup');
    });

    Route::controller(ForgotPasswordController::class)->prefix('password')->group(function () {
        Route::get('/request', 'showForgotPassForm')->name('password.request');
        Route::post('/email', 'sendPasswordResetLink')->name('password.email');
        Route::get('/reset/{token}', 'showResetForm')->name('password.reset');
        Route::post('/reset', 'reset')->name('password.update');

        // Additional authentication flows
        Route::post('/resend', 'verificationSend')->name('verification.send');
        Route::post('/confirm', 'passwordConfirm')->name('password.confirm');
        Route::post('/challenge', 'twoFactorChallenge')->name('two-factor.login');
    });

    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->intended(route('dashboard'))
            ->with('verified', true);
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::get('/', [AdminController::class, 'landing']);
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name("home");
    Route::get('dashboard', [ClientController::class, 'dashboard'])->name("client.dashboard");

    Route::group(['middleware' => 'web'], function () {
        Route::group(['middleware' => 'dashboard'], function () {
            Route::group(['middleware' => 'csrf'], function () {
                Route::group([ 'prefix' => ''], function() {

                    includeRouteFiles(__DIR__ . '/Web/');
                });
            });
        });
    });
});
