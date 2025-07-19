<?php

namespace Modules\User\Repositories\Api\V1\Eloquent;

use Modules\User\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Repositories\Api\V1\Interfaces\AdminRepositoryInterface;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * Paginate the admins.
     *
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return Admin::paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Create a new admin.
     *
     * @param array $data
     * @return Admin
     */
    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    /**
     * Find admin by ID.
     *
     * @param int $id
     * @return Admin|null
     */
    public function find(int $id): ?Admin
    {
        return Admin::find($id);
    }

    /**
     * Update admin by ID.
     *
     * @param int $id
     * @param array $data
     * @return Admin|null
     */
    public function update(int $id, array $data): ?Admin
    {
        $admin = $this->find($id);

        if (!$admin) {
            return null;
        }

        $admin->update($data);

        return $admin;
    }
   /**
     * Delete admin by ID or model instance.
     *
     * @param int|Admin $adminOrId
     * @return bool
     */
    public function delete(Admin|int $adminOrId): bool
    {
        if ($adminOrId instanceof Admin) {
            return $adminOrId->delete();
        }

        $admin = $this->find($adminOrId);

        if (!$admin) {
            return false;
        }

        return $admin->delete();
    }
}
