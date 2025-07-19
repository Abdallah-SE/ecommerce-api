<?php

namespace Modules\Core\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Modules\Core\Helpers\ApiResponse;

class BaseApiController extends Controller
{
    protected function respondSuccess($data = null, string $message = 'Success', int $status = 200, array $meta = [])
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function respondError(string $message = 'Error', int $status = 400, $errors = null)
    {
        return ApiResponse::error($message, $status, $errors);
    }

    protected function respondUnauthorized(string $message = 'Unauthorized')
    {
        return ApiResponse::unauthorized($message);
    }

    protected function respondNotFound(string $message = 'Not Found')
    {
        return ApiResponse::notFound($message);
    }

    protected function respondValidationError(array $errors, string $message = 'Validation failed')
    {
        return ApiResponse::validationError($errors, $message);
    }
}
