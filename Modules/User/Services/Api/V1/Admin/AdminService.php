<?php

namespace Modules\User\Services\Api\V1\Admin;

use Modules\User\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Modules\User\Exceptions\AdminNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Repositories\Api\V1\Interfaces\AdminRepositoryInterface;

class AdminService
{
    public const DEFAULT_PER_PAGE = 15;
    public const MAX_PER_PAGE = 100;

    protected AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Paginate admin users.
     *
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function paginateAdmins(int $perPage, int $page): LengthAwarePaginator
    {
        $perPage = ($perPage < 1 || $perPage > self::MAX_PER_PAGE) ? self::DEFAULT_PER_PAGE : $perPage;
        $page = max($page, 1);

        return $this->adminRepository->paginate($perPage, $page);
    }

    /**
     * Create a new admin user.
     *
     * @param array $data
     * @return Admin
     */
    public function createAdmin(array $data): Admin
    {
        $data['password'] = Hash::make($data['password']);
        return $this->adminRepository->create($data);
    }

    /**
     * Find an admin by ID or throw exception if not found.
     *
     * @param int $id
     * @return Admin
     *
     * @throws AdminNotFoundException
     */
    public function findAdminOrFail(int $id): Admin
    {
        $admin = $this->adminRepository->find($id);

        if (!$admin) {
            throw new AdminNotFoundException("Admin with id {$id} not found.");
        }

        return $admin;
    }

    /**
     * Update an admin user.
     *
     * @param int $id
     * @param array $data
     * @return Admin
     *
     * @throws AdminNotFoundException
     */
    public function updateAdmin(int $id, array $data): Admin
    {
        $this->findAdminOrFail($id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $updatedAdmin = $this->adminRepository->update($id, $data);

        if (!$updatedAdmin) {
            // This is a fallback - ideally this shouldn't happen
            throw new AdminNotFoundException("Failed to update admin with id {$id}.");
        }

        return $updatedAdmin;
    }

    /**
     * Delete an admin user or throw exception if not found.
     *
     * @param int $id
     * @return bool
     *
     * @throws AdminNotFoundException
     */
    public function deleteAdminOrFail(int $id): bool
    {
        $admin =  $this->findAdminOrFail($id);
        return $this->adminRepository->delete($admin);
    }
}
