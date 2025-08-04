<?php

namespace Modules\Core\Exceptions\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Modules\Core\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

/**
 * Trait for handling exceptions in a consistent manner across the application.
 * 
 * This trait provides methods to handle different types of exceptions
 * and return standardized JSON responses.
 * 
 * @package Modules\Core\Exceptions\Traits
 */
trait ExceptionHandlerTrait
{
    /**
     * Handle different types of exceptions and return consistent JSON responses.
     *
     * @param \Throwable $exception The exception to handle
     * @return JsonResponse
     */
    protected function handleException(\Throwable $exception): JsonResponse
    {
        // Log the exception for debugging (but don't expose it to user)
        $this->logException($exception);

        // Handle API exceptions with custom format
        if ($exception instanceof ApiException) {
            return $this->renderApiException($exception);
        }

        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return $this->renderValidationException($exception);
        }

        // Handle authentication exceptions
        if ($exception instanceof AuthenticationException) {
            return $this->renderAuthenticationException($exception);
        }

        // Handle model not found exceptions
        if ($exception instanceof ModelNotFoundException) {
            return $this->renderModelNotFoundException($exception);
        }

        // Handle not found HTTP exceptions
        if ($exception instanceof NotFoundHttpException) {
            return $this->renderNotFoundHttpException($exception);
        }

        // Handle method not allowed exceptions
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->renderMethodNotAllowedException($exception);
        }

        // Handle database query exceptions
        if ($exception instanceof QueryException) {
            return $this->renderQueryException($exception);
        }

        // Handle general exceptions
        return $this->renderGeneralException($exception);
    }

    /**
     * Log exception details for debugging.
     *
     * @param \Throwable $exception
     * @return void
     */
    protected function logException(\Throwable $exception): void
    {
        Log::error('Exception occurred: ' . $exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Render API exceptions with consistent format.
     *
     * @param ApiException $exception The API exception to render
     * @return JsonResponse
     */
    protected function renderApiException(ApiException $exception): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $exception->getMessage(),
            'code' => $exception->getErrorCode(),
            'data' => null,
        ];

        // Add errors if they exist in context
        $context = $exception->getContext();
        if (!empty($context['errors'])) {
            $response['errors'] = $context['errors'];
        }

        // Only add debug information in development mode
        if (config('app.debug') && !empty($context)) {
            $response['debug'] = $context;
        }

        return response()->json($response, $exception->getStatusCode());
    }

    /**
     * Render validation exceptions.
     *
     * @param ValidationException $exception The validation exception to render
     * @return JsonResponse
     */
    protected function renderValidationException(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation failed',
            'code' => 'validation.failed',
            'data' => null,
            'errors' => $exception->errors(),
        ], 422);
    }

    /**
     * Render authentication exceptions.
     *
     * @param AuthenticationException $exception The authentication exception to render
     * @return JsonResponse
     */
    protected function renderAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated',
            'code' => 'auth.unauthenticated',
            'data' => null,
        ], 401);
    }

    /**
     * Render model not found exceptions.
     *
     * @param ModelNotFoundException $exception The model not found exception to render
     * @return JsonResponse
     */
    protected function renderModelNotFoundException(ModelNotFoundException $exception): JsonResponse
    {
        $model = class_basename($exception->getModel());
        
        return response()->json([
            'status' => false,
            'message' => "{$model} not found",
            'code' => 'model.not_found',
            'data' => null,
        ], 404);
    }

    /**
     * Render not found HTTP exceptions.
     *
     * @param NotFoundHttpException $exception The not found HTTP exception to render
     * @return JsonResponse
     */
    protected function renderNotFoundHttpException(NotFoundHttpException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Resource not found',
            'code' => 'resource.not_found',
            'data' => null,
        ], 404);
    }

    /**
     * Render method not allowed exceptions.
     *
     * @param MethodNotAllowedHttpException $exception The method not allowed exception to render
     * @return JsonResponse
     */
    protected function renderMethodNotAllowedException(MethodNotAllowedHttpException $exception): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Method not allowed',
            'code' => 'method.not_allowed',
            'data' => null,
        ], 405);
    }

    /**
     * Render database query exceptions.
     *
     * @param QueryException $exception The query exception to render
     * @return JsonResponse
     */
    protected function renderQueryException(QueryException $exception): JsonResponse
    {
        // Log the actual error for debugging
        Log::error('Database query exception: ' . $exception->getMessage(), [
            'sql' => $exception->getSql(),
            'bindings' => $exception->getBindings(),
            'trace' => $exception->getTraceAsString(),
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Database error occurred',
            'code' => 'database.error',
            'data' => null,
        ], 500);
    }

    /**
     * Render general exceptions.
     *
     * @param \Throwable $exception The general exception to render
     * @return JsonResponse
     */
    protected function renderGeneralException(\Throwable $exception): JsonResponse
    {
        // Determine the appropriate message based on environment
        $message = config('app.debug') 
            ? $exception->getMessage() 
            : 'An unexpected error occurred';

        // Determine status code based on exception type
        $statusCode = $this->getStatusCodeForException($exception);

        return response()->json([
            'status' => false,
            'message' => $message,
            'code' => 'error.general',
            'data' => null,
        ], $statusCode);
    }

    /**
     * Get appropriate status code for exception.
     *
     * @param \Throwable $exception
     * @return int
     */
    protected function getStatusCodeForException(\Throwable $exception): int
    {
        // Map common exceptions to appropriate status codes
        $statusCodeMap = [
            'Illuminate\Database\Eloquent\ModelNotFoundException' => 404,
            'Illuminate\Auth\AuthenticationException' => 401,
            'Illuminate\Auth\Access\AuthorizationException' => 403,
            'Illuminate\Validation\ValidationException' => 422,
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 404,
            'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException' => 405,
            'Illuminate\Database\QueryException' => 500,
        ];

        $exceptionClass = get_class($exception);
        
        return $statusCodeMap[$exceptionClass] ?? 500;
    }
}