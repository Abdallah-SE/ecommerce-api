<?php

namespace App\Exceptions;

use Throwable;
use Modules\Core\Exceptions\ApiException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
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
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ApiException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'code' => $exception->getErrorCode(),
                'errors' => $exception->getContext()['errors'] ?? null,
            ], $exception->getStatusCode());
        }

        return parent::render($request, $exception);
    }
}
