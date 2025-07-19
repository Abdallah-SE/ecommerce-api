<?php

use Illuminate\Support\Str;

if (!function_exists('api_success')) {
    function api_success($data = null, string $message = 'Success', int $status = 200, array $meta = [])
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta ?: null
        ], $status);
    }
}

if (!function_exists('api_error')) {
    function api_error(string $message = 'Error', int $status = 400, $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}

if (!function_exists('is_json')) {
    function is_json($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
