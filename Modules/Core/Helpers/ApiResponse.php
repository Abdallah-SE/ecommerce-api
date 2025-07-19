<?php

namespace Modules\Core\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiResponse
{

    /**
     * Format a successful JSON response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    public static function success($data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    /**
     * Return a successful response with optional pagination meta.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondSuccess($data = null, string $message = 'Success', int $status = 200, array $meta = [])
    {
        // Check if data is paginated
        if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
            // Extract pagination info
            $pagination = [
                'current_page' => $data->currentPage(),
                'last_page' => method_exists($data, 'lastPage') ? $data->lastPage() : null,
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'has_more_pages' => method_exists($data, 'hasMorePages') ? $data->hasMorePages() : null,
            ];

            // Replace data with collection of items
            $data = $data->items();

            // Merge pagination info into meta
            $meta = array_merge($meta, ['pagination' => $pagination]);
        }

        return ApiResponse::success($data, $message, $status, $meta);
    }

    public static function error(string $message = 'Something went wrong', int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, 401);
    }

    public static function notFound(string $message = 'Not found'): JsonResponse
    {
        return self::error($message, 404);
    }

    public static function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return self::error($message, 422, $errors);
    }
}
