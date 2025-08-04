<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Core\Exceptions\Traits\ExceptionHandlerTrait;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomExceptionHandler
{
    use ExceptionHandlerTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug: Log that middleware is being called
        Log::info('CustomExceptionHandler middleware is being called for: ' . $request->url());
        
        try {
            return $next($request);
        } catch (Throwable $exception) {
            // Debug: Log what type of exception we're catching
            Log::info('CustomExceptionHandler caught exception: ' . get_class($exception));
            Log::info('Exception message: ' . $exception->getMessage());

            // Log the exception for debugging (but don't expose to user)
            Log::error('Exception occurred: ' . $exception->getMessage(), [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            // Handle our custom ApiException
            if ($exception instanceof \Modules\Core\Exceptions\ApiException) {
                Log::info('Handling ApiException: ' . $exception->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getErrorCode(),
                    'data' => null,
                ], $exception->getStatusCode());
            }

            // Handle validation exceptions
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'code' => 'validation.failed',
                    'data' => null,
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Handle authentication exceptions
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated',
                    'code' => 'auth.unauthenticated',
                    'data' => null,
                ], 401);
            }

            // Handle model not found exceptions
            if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $model = class_basename($exception->getModel());
                return response()->json([
                    'status' => false,
                    'message' => "{$model} not found",
                    'code' => 'model.not_found',
                    'data' => null,
                ], 404);
            }

            // Handle general exceptions with clean messages
            $message = config('app.debug') 
                ? $exception->getMessage() 
                : 'An unexpected error occurred';

            return response()->json([
                'status' => false,
                'message' => $message,
                'code' => 'error.general',
                'data' => null,
            ], 500);
        }
    }
} 