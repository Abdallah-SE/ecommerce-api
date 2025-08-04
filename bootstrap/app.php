<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Override Laravel's default exception handling for JSON responses
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                // Log the exception for debugging
                \Illuminate\Support\Facades\Log::error('Exception occurred: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Handle our custom ApiException
                if ($e instanceof \Modules\Core\Exceptions\ApiException) {
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                        'code' => $e->getErrorCode(),
                        'data' => null,
                    ], $e->getStatusCode());
                }

                // Handle general exceptions
                $message = config('app.debug') 
                    ? $e->getMessage() 
                    : 'An unexpected error occurred';

                return response()->json([
                    'status' => false,
                    'message' => $message,
                    'code' => 'error.general',
                    'data' => null,
                ], 500);
            }
        });
    })->create();
