<?php
namespace Modules\Core\Exceptions;
use Exception;

abstract class ApiException extends Exception
{
    protected int $statusCode;
    protected string $errorCode;
    protected array $context;

    public function __construct(string $message = "", string $errorCode = "", int $statusCode = 400, array $context = [])
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errorCode = $errorCode ?: 'error.general';
        $this->context = $context;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
