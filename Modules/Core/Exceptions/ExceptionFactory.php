<?php

namespace Modules\Core\Exceptions;

/**
 * Factory class for creating standardized API exceptions.
 * 
 * This factory provides methods to create consistent exceptions
 * across the application with proper error codes and status codes.
 * 
 * @package Modules\Core\Exceptions
 */
class ExceptionFactory
{
    /**
     * Create a not found exception for any entity.
     *
     * @param string      $entity  The entity name (e.g., 'User', 'Product')
     * @param int|null    $id      The entity ID (optional)
     * @param string|null $message Custom message (optional)
     * @return ApiException
     */
    public static function notFound(string $entity, int $id = null, string $message = null): ApiException
    {
        $defaultMessage = $id 
            ? "{$entity} with ID {$id} not found." 
            : "{$entity} not found.";
        
        $message = $message ?: $defaultMessage;
        
        $context = [];
        if ($id) {
            $context['entity_id'] = $id;
        }
        
        return new ApiException($message, strtolower($entity) . '.not_found', 404, $context);
    }

    /**
     * Create a creation failed exception.
     *
     * @param string      $entity  The entity name
     * @param string|null $message Custom message (optional)
     * @param array       $context Additional context (optional)
     * @return ApiException
     */
    public static function creationFailed(string $entity, string $message = null, array $context = []): ApiException
    {
        $defaultMessage = "Failed to create {$entity}.";
        $message = $message ?: $defaultMessage;
        
        return new ApiException($message, strtolower($entity) . '.creation_failed', 500, $context);
    }

    /**
     * Create an update failed exception.
     *
     * @param string      $entity  The entity name
     * @param int|null    $id      The entity ID (optional)
     * @param string|null $message Custom message (optional)
     * @param array       $context Additional context (optional)
     * @return ApiException
     */
    public static function updateFailed(string $entity, int $id = null, string $message = null, array $context = []): ApiException
    {
        $defaultMessage = $id 
            ? "Failed to update {$entity} with ID {$id}." 
            : "Failed to update {$entity}.";
        
        $message = $message ?: $defaultMessage;
        
        if ($id) {
            $context['entity_id'] = $id;
        }
        
        return new ApiException($message, strtolower($entity) . '.update_failed', 500, $context);
    }

    /**
     * Create a deletion failed exception.
     *
     * @param string      $entity  The entity name
     * @param int|null    $id      The entity ID (optional)
     * @param string|null $message Custom message (optional)
     * @param array       $context Additional context (optional)
     * @return ApiException
     */
    public static function deletionFailed(string $entity, int $id = null, string $message = null, array $context = []): ApiException
    {
        $defaultMessage = $id 
            ? "Failed to delete {$entity} with ID {$id}." 
            : "Failed to delete {$entity}.";
        
        $message = $message ?: $defaultMessage;
        
        if ($id) {
            $context['entity_id'] = $id;
        }
        
        return new ApiException($message, strtolower($entity) . '.deletion_failed', 500, $context);
    }

    /**
     * Create a validation exception with detailed errors.
     *
     * @param array  $errors Validation errors array
     * @param string $message Custom message (optional)
     * @return ApiException
     */
    public static function validation(array $errors, string $message = 'Validation failed'): ApiException
    {
        return new ApiException($message, 'validation.failed', 422, ['errors' => $errors]);
    }

    /**
     * Create an unauthorized exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function unauthorized(string $message = 'Unauthorized', array $context = []): ApiException
    {
        return new ApiException($message, 'auth.unauthorized', 401, $context);
    }

    /**
     * Create a forbidden exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function forbidden(string $message = 'Forbidden', array $context = []): ApiException
    {
        return new ApiException($message, 'auth.forbidden', 403, $context);
    }

    /**
     * Create a bad request exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function badRequest(string $message = 'Bad request', array $context = []): ApiException
    {
        return new ApiException($message, 'request.bad', 400, $context);
    }

    /**
     * Create a conflict exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function conflict(string $message = 'Conflict', array $context = []): ApiException
    {
        return new ApiException($message, 'request.conflict', 409, $context);
    }

    /**
     * Create a server error exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function serverError(string $message = 'Internal server error', array $context = []): ApiException
    {
        return new ApiException($message, 'server.error', 500, $context);
    }

    /**
     * Create a service unavailable exception.
     *
     * @param string $message Custom message (optional)
     * @param array  $context Additional context (optional)
     * @return ApiException
     */
    public static function serviceUnavailable(string $message = 'Service unavailable', array $context = []): ApiException
    {
        return new ApiException($message, 'service.unavailable', 503, $context);
    }

    /**
     * Create a custom exception with full control over parameters.
     *
     * @param string $message    The exception message
     * @param string $errorCode  The error code
     * @param int    $statusCode The HTTP status code
     * @param array  $context    Additional context
     * @return ApiException
     */
    public static function custom(string $message, string $errorCode, int $statusCode, array $context = []): ApiException
    {
        return new ApiException($message, $errorCode, $statusCode, $context);
    }
}