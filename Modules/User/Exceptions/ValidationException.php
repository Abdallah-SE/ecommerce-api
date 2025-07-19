<?php

namespace Modules\User\Exceptions;

use Modules\Core\Exceptions\ApiException;

class ValidationException extends ApiException
{
    public function __construct(array $errors, string $message = "Validation failed")
    {
        parent::__construct($message, 'validation.failed', 422, ['errors' => $errors]);
    }
}