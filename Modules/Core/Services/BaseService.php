<?php

namespace Modules\Core\Services;

use Modules\Core\Exceptions\ExceptionFactory;

abstract class BaseService
{
    /**
     * Find an entity by ID or throw exception if not found.
     */
    protected function findOrFail(string $entity, int $id, $repository)
    {
        $model = $repository->find($id);

        if (!$model) {
            throw ExceptionFactory::notFound($entity, $id);
        }

        return $model;
    }

    /**
     * Create an entity or throw exception if failed.
     */
    protected function createOrFail(string $entity, array $data, $repository, callable $createCallback = null)
    {
        try {
            if ($createCallback) {
                return $createCallback($data);
            }
            
            return $repository->create($data);
        } catch (\Exception $e) {
            throw ExceptionFactory::creationFailed($entity, null, [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Update an entity or throw exception if failed.
     */
    protected function updateOrFail(string $entity, int $id, array $data, $repository, callable $updateCallback = null)
    {
        $this->findOrFail($entity, $id, $repository);

        try {
            if ($updateCallback) {
                return $updateCallback($id, $data);
            }
            
            return $repository->update($id, $data);
        } catch (\Exception $e) {
            throw ExceptionFactory::updateFailed($entity, $id, null, [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Delete an entity or throw exception if failed.
     */
    protected function deleteOrFail(string $entity, int $id, $repository, callable $deleteCallback = null)
    {
        $model = $this->findOrFail($entity, $id, $repository);

        try {
            if ($deleteCallback) {
                return $deleteCallback($model);
            }
            
            return $repository->delete($model);
        } catch (\Exception $e) {
            throw ExceptionFactory::deletionFailed($entity, $id, null, [
                'error' => $e->getMessage()
            ]);
        }
    }
}