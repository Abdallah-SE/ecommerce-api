<?php

namespace Modules\User\Exceptions;

use Modules\Core\Exceptions\ApiException;

class UnauthorizedException extends ApiException
{
    public function __construct(string $message = "Unauthorized", array $context = [])
    {
        parent::__construct($message, 'auth.unauthorized', 401, $context);
    }
}