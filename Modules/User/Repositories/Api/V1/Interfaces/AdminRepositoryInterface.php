<?php

namespace Modules\User\Repositories\Api\V1\Interfaces;

use Modules\User\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface
{
    public function paginate(int $perPage, int $page): LengthAwarePaginator;

    public function create(array $data);

    public function find(int $id);

    public function update(int $id, array $data);

    /**
     * Delete by Admin instance or id
     *
     * @param int|Admin $adminOrId
     * @return bool
     */
    public function delete(Admin|int $adminOrId): bool;
}
