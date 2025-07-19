<?php

namespace Modules\User\Exceptions;

use Modules\Core\Exceptions\ApiException;

class AdminNotFoundException extends ApiException
{
    public function __construct(string $message = "Admin not found", array $context = [])
    {
        parent::__construct($message, 'admin.not_found', 404, $context);
    }
}
