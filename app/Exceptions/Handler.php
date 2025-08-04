<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Modules\Core\Exceptions\Traits\ExceptionHandlerTrait;
use Modules\Core\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    use ExceptionHandlerTrait;

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Report or log an exception.
     */
    public function report(Throwable $exception): void
    {
        // Log all exceptions for debugging
        Log::error('Exception occurred: ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Always use our custom exception handler for consistent responses
        return $this->handleException($exception);
    }

    /**
     * Handle unauthenticated exceptions.
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated',
            'code' => 'auth.unauthenticated',
            'data' => null,
        ], 401);
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}