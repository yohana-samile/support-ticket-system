<?php

namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->environment('production')) {
                Log::error('Server Error: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);
            }
            Log::error('[' . $e->getCode() . '] ' . $e->getMessage() . ' on line ' . @$e->getLine() . ' of file ' . @$e->getFile());
            return false; // Exclude the long unnecessary error stack ...
        });
    }

    public function render($request, Throwable $e)
    {
        if (config('app.debug')) {
            Log::error('[' . $e->getCode() . '] ' . $e->getMessage() . ' on line ' . @$e->getLine() . ' of file ' . @$e->getFile());
        }

        Log::error(get_class($e));
        if ($this->isHttpException($e)) {
            Log::error('Caught HttpException: ' . $e->getStatusCode());

            if ($e->getStatusCode() === 401) {
                return response()->view('errors.401', [], 401);
            }

            if ($e->getStatusCode() === 404) {
                return response()->view('errors.404', [], 404);
            }

            if ($e->getStatusCode() === 503) {
                return response()->view('errors.503', [], 503);
            }

            if ($e->getStatusCode() === 500) {
                return response()->view('errors.500', [], 500);
            }
        }

        return parent::render($request, $e);
    }
}
