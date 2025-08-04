<?php

namespace Modules\Core\Services\Traits;

use Modules\Core\Exceptions\ExceptionFactory;

trait ServiceHelperTrait
{
    /**
     * Validate and sanitize pagination parameters.
     */
    protected function validatePagination(int $perPage, int $page, int $maxPerPage = 100, int $defaultPerPage = 15): array
    {
        $perPage = ($perPage < 1 || $perPage > $maxPerPage) ? $defaultPerPage : $perPage;
        $page = max($page, 1);

        return [$perPage, $page];
    }

    /**
     * Handle password hashing if present in data.
     */
    protected function handlePassword(array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = \Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * Validate entity existence and throw appropriate exception.
     */
    protected function validateEntityExists(string $entity, int $id, $repository): void
    {
        if (!$repository->find($id)) {
            throw ExceptionFactory::notFound($entity, $id);
        }
    }

    /**
     * Log operation for debugging.
     */
    protected function logOperation(string $operation, string $entity, array $context = []): void
    {
        \Log::info("{$operation} {$entity}", $context);
    }
}