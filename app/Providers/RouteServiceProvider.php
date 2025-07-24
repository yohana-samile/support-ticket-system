<?php

namespace App\Providers;

use App\Models\SaasApp;
use App\Models\SenderId;
use App\Models\SubTopic;
use App\Models\Topic;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot()
    {
        parent::boot();
        Route::bind('topic', function ($value) {
            return Topic::where('uid', $value)->firstOrFail();
        });
        Route::bind('subtopic', function ($value) {
            return SubTopic::where('uid', $value)->firstOrFail();
        });
        Route::bind('saasApp', function ($value) {
            return SaasApp::where('uid', $value)->firstOrFail();
        });
        Route::bind('sender', function ($value) {
            return SenderId::where('uid', $value)->firstOrFail();
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

//            Route::middleware('web')
//                ->group(base_path('routes/web.php'));
            Route::namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }
}
