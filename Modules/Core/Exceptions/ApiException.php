<?php

namespace Modules\Core\Exceptions;

use Exception;

/**
 * Base API Exception class for consistent error handling across the application.
 * 
 * This class provides a standardized way to handle API exceptions with
 * consistent error codes, status codes, and context information.
 * 
 * @package Modules\Core\Exceptions
 */
class ApiException extends Exception
{
    /**
     * HTTP status code for the exception.
     */
    protected int $statusCode;

    /**
     * Error code for the exception.
     */
    protected string $errorCode;

    /**
     * Additional context information for the exception.
     */
    protected array $context;

    /**
     * Create a new API exception instance.
     *
     * @param string $message    The exception message
     * @param string $errorCode  The error code for the exception
     * @param int    $statusCode The HTTP status code
     * @param array  $context    Additional context information
     */
    public function __construct(
        string $message = "",
        string $errorCode = "",
        int $statusCode = 400,
        array $context = []
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode ?: 'error.general';
        $this->context = $context;
    }

    /**
     * Get the HTTP status code for the exception.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the error code for the exception.
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the context information for the exception.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set additional context information.
     *
     * @param array $context
     * @return void
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * Add context information to existing context.
     *
     * @param array $context
     * @return void
     */
    public function addContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }
}
